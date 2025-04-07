import os
import re
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
from openai import OpenAI
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

app = Flask(__name__)
CORS(app)

MASONIC_DATA_FOLDER = 'masonicDocs'
CSV_FILES = [
    'masonicData.csv'
]

# initializing OpenAI
ai_client = None
try:
    # getting API key from env file
    openai_api_key = os.getenv('OPENAI_API_KEY')

    if not openai_api_key:
        print("ERROR: OPENAI_API_KEY environment variable not set or empty in .env file.")
    else:
        ai_client = OpenAI(api_key=openai_api_key)
        print("OpenAI client initialized successfully.")
        
except Exception as e:
    print(f"Error initializing OpenAI client: {e}")

'''
this function is used to load the data for the masonic questions
it takes no input and returns a dictionary of dataframes
'''
def load_masonic_data():
    dataframes = {}
    # get the current directory
    base_dir = os.path.dirname(os.path.abspath(__file__))
    # get the path of the masonic data folder
    data_path = os.path.join(base_dir, MASONIC_DATA_FOLDER)

    if not os.path.isdir(data_path):
        print(f"Error: Masonic data folder not found at {data_path}")
        return {}

    try:
        for filename in CSV_FILES:
            # creates file path for each file
            filepath = os.path.join(data_path, filename)
            if os.path.exists(filepath):
                try:
                    # creates a pandas df for each file
                    df = pd.read_csv(filepath, dtype={'Procedure': str, 'Definition': str})
                    df['Procedure_lower'] = df['Procedure'].str.lower().str.strip()
                    dataframes[filename] = df
                    
                # gives error message if the file is not found
                except Exception as e:
                     print(f"Error reading or processing CSV file {filename}: {e}")

            else:
                # data not found at the path
                print(f"Warning: CSV file not found at {filepath}")
                
        return dataframes
    
    except Exception as e2:
        # data did not load correctly
        print(f"Error loading Masonic data: {e2}")
        return {}

masonic_data = load_masonic_data()

"""
this function splits the text into sentences based on common delimiters
"""
def split_into_sentences(text):
    sentences = re.split(r'(?<=[.!?])(?:\s+|\n+|$)|(?<=\n)(?=\d+\.\s|\* |- |\u2022\s)', text)
    return [s.strip() for s in sentences if s and s.strip()]

"""
this function searches the masonic data for keywords from the user input
if a procedure in the masonic data contains a keyword from the user input
then the definition of that procedure is added to the list of definitions
"""
def find_masonic_definitions(user_input):
    definitions = []
    user_input_lower = user_input.lower()
    user_words = set(re.findall(r'\b\w{3,}\b', user_input_lower))

    if not masonic_data:
        return ["Sorry, I couldn't load the Masonic policy data. Please check server logs."]

    found_match = False
    matched_procedures_lower = set()

    for filename, df in masonic_data.items():
        for index, row in df.iterrows():
            try:
                procedure_text_lower = row['Procedure_lower']
                original_procedure_text = row['Procedure']
                definition_text = row['Definition']

                # if a user input word is in the procedure text and the procedure text has not been matched before then add the definition to the list
                if any(word in procedure_text_lower for word in user_words):
                    if procedure_text_lower not in matched_procedures_lower:
                        sentences = split_into_sentences(definition_text)
                        if sentences:
                            definitions.extend(sentences)
                            matched_procedures_lower.add(procedure_text_lower)
                            found_match = True
                        else:
                             print(f"Warning: Matched procedure '{original_procedure_text}' in {filename} but its definition was empty or unsplittable.")
            except Exception as row_e:
                 print(f"Error processing row {index} in {filename}: {row_e}")

    if not found_match:
        # prints if the user input does not match any of the procedures
        return ["I couldn't find specific information related to your query in the Masonic policies. Could you please rephrase or ask about a different topic?"]

    unique_definitions = list(dict.fromkeys(definitions))
    return unique_definitions


"""
this function uses the OpenAI API to answer general health questions
the logic is handled by AI based on the prompt, not forced by Python code
"""
def get_general_health_response(user_input):
    if not ai_client:
         return ["Sorry, the AI assistant is currently unavailable. Please check the server configuration or try again later."]

    # --- Construct the Prompt for the AI ---
    system_prompt = """You are a helpful AI assistant embedded in a chatbot on a medical website.
Your purpose is to provide general health information clearly and concisely.

**Key Instructions:**
1.  **Analyze the User's Question:** Understand the core health topic the user is asking about.
2.  **Provide General Information:** Offer factual, easy-to-understand information related to the question.
3.  **DO NOT DIAGNOSE:** Never attempt to diagnose medical conditions.
4.  **DO NOT PRESCRIBE/RECOMMEND TREATMENT:** Do not suggest specific medications, therapies, or treatment plans.
5.  **MANDATORY DISCLAIMER:** ALWAYS conclude your response by strongly advising the user to consult a qualified healthcare professional for personal medical advice, diagnosis, or treatment. State the disclaimer clearly and directly. Example: "Remember, this is general information. Please consult with a qualified healthcare provider for personal health concerns."
6.  **Keep it Concise:** Aim for brief, informative answers (around 2-4 clear sentences before the disclaimer unless the question requires more detail). Avoid overly long explanations.
7.  **Format for Clarity:** Use clear sentence structure. The calling code will handle splitting into list items.
"""

    user_message_content = f"User question: '{user_input}'"

    messages = [
        {"role": "system", "content": system_prompt},
        {"role": "user", "content": user_message_content}
    ]

    try:
        completion = ai_client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=messages,
            max_tokens=200,
            temperature=0.5,
            n=1,
            stop=None
        )
        raw_ai_answer = completion.choices[0].message.content.strip()
        response_sentences = split_into_sentences(raw_ai_answer)
        response_sentences = [s for s in response_sentences if s]

        if not response_sentences or "cannot provide medical advice" in raw_ai_answer.lower() or len(raw_ai_answer) < 50:
             return ["I understand you're asking about that topic. However, as an AI, I cannot provide specific medical advice or diagnosis."]

        final_response = response_sentences

        return final_response

    except Exception as e:
        print(f"Error calling OpenAI API: {e}")
        return ["Sorry, I encountered an issue while trying to generate a response. This might be a temporary problem. Please try asking again in a moment."]


@app.route('/chat', methods=['POST'])
def chat_endpoint():
    """
    this function is the endpoint for the chatbot
    it takes a JSON input and returns a JSON output
    """
    try:
        data = request.get_json()
        if not data:
            return jsonify({'reply': ["Invalid request format."]}), 400

        user_message = data.get('message', '').strip()
        # mode will be equal to 'general' or 'masonic'
        mode = data.get('mode')

        if not user_message:
            return jsonify({'reply': ["Please type a question."]})
        if not mode or mode not in ['general', 'masonic']:
             return jsonify({'reply': ["Error: Invalid mode specified. Choose 'General Health' or 'Masonic Policies'."]}), 400


        reply_content = []
        if mode == 'general':
            reply_content = get_general_health_response(user_message)
        elif mode == 'masonic':
            reply_content = find_masonic_definitions(user_message)

        if not isinstance(reply_content, list):
            reply_content = [str(reply_content)]
        reply_content = [item for item in reply_content if isinstance(item, str) and item]

        if not reply_content:
            reply_content = ["Sorry, I could not find an answer for that."]

        return jsonify({'reply': reply_content})

    except Exception as e:
        return jsonify({'reply': ["Sorry, an unexpected server error occurred. Please try again later."]}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)
