from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
import faiss
import os
import re
from sentence_transformers import SentenceTransformer
from flask_cors import CORS

# Initialize Flask application
app = Flask(__name__)
CORS(app)

# Loading the Sentence Transformer model
# this model is used to encode the questions and queries into vectors for similarity search
print("Loading sentence transformer model...")
model = SentenceTransformer("all-MiniLM-L6-v2")

# Constants for data and embeddings
DATA_FOLDER = "chatbotData"
EMBEDDINGS_FILE = "question_embeddings.npy"
CSV_DATA_FILE = "merged_data.csv"

# Load the dataset
if not os.path.exists(CSV_DATA_FILE):
    all_data = []
    for file in os.listdir(DATA_FOLDER):
        if file.endswith(".csv"):
            df = pd.read_csv(os.path.join(DATA_FOLDER, file))
            all_data.append(df)

    df = pd.concat(all_data, ignore_index=True)
    df.drop(columns=["split"], errors="ignore", inplace=True)
    df.to_csv(CSV_DATA_FILE, index=False)  # Save for future use
else:
    df = pd.read_csv(CSV_DATA_FILE)

# checking for the correct columns
if not all(col in df.columns for col in ["Question", "Answer", "topic"]):
    raise ValueError("Missing required columns in dataset: 'Question', 'Answer', 'topic'")

# turning the columns into lists
questions = df["Question"].astype(str).tolist()
answers = df["Answer"].astype(str).tolist()
qtypes = df["topic"].astype(str).tolist()

# loading the precomputed embeddings if they exist
if os.path.exists(EMBEDDINGS_FILE):
    print("Loading precomputed embeddings...")
    question_embeddings = np.load(EMBEDDINGS_FILE).astype(np.float32)
else:
    print("Generating new question embeddings...")
    question_embeddings = model.encode(questions, convert_to_tensor=False, show_progress_bar=True)
    np.save(EMBEDDINGS_FILE, question_embeddings)

# creating a FAISS index for similarity search
# FAISS is a library for efficient similarity search and clustering of dense vectors
dimension = question_embeddings.shape[1]
faiss_index = faiss.IndexFlatL2(dimension)
faiss_index.add(question_embeddings)

# load the keywords.csv file into a new dataframe
keywords_df = pd.read_csv("masonicDocs/keywords.csv")

# load the falls policies csv file into a new dataframe
falls_policies_df = pd.read_csv("masonicDocs/falls_policy.csv")

# if falls policies is selected, ask user if they need help with a keyword definition
@app.route("/keyword_definition", methods=["POST"])
def keyword_definition():
    data = request.json
    keyword = data.get("keyword", "").strip()
    
    if not keyword:
        return jsonify({"Definition": "Please provide a keyword."})

    # Ensure column names are in correct case & remove extra spaces
    keywords_df.columns = keywords_df.columns.str.strip()
    
    if "Keyword" not in keywords_df.columns or "Definition" not in keywords_df.columns:
        return jsonify({"Definition": "Error: Missing 'Keyword' or 'Definition' column in keywords.csv."})

    # Convert column to lowercase & strip spaces for consistent searching
    keywords_df["Keyword"] = keywords_df["Keyword"].astype(str).str.lower().str.strip()
    keywords_df["Definition"] = keywords_df["Definition"].astype(str).str.strip()
    
    keyword_lower = keyword.lower().strip()

    # Search for keyword (full match or partial match)
    match = keywords_df[keywords_df["Keyword"].str.contains(keyword_lower, case=False, na=False)]

    if not match.empty:
        definition = match.iloc[0]["Definition"]

        # Split the definition into sentences
        sentence_list = re.split(r'(?<=[.!?])\s+', definition)  # Splits at periods, exclamations, or question marks

        # Format the output
        formatted_response = f"<strong>{keyword.capitalize()}:</strong><ul>"
        for sentence in sentence_list:
            if sentence.strip():  # Ignore empty strings
                formatted_response += f"<li>{sentence.strip()}</li>"
        formatted_response += "</ul>"

        return jsonify({"Definition": formatted_response})

    return jsonify({"Definition": "No definition found for the keyword."})


# if falls policy and no keyword is selected, get user input and find response in falls policies csv
@app.route("/get_falls_policy", methods=["POST"])
def get_falls_policy():
    data = request.json
    procedure = data.get("procedure", "").strip()
    
    if not procedure:
        return jsonify({"Definition": "Please provide a procedure name."})

    # Ensure column names are correctly formatted
    falls_policies_df.columns = falls_policies_df.columns.str.strip()

    if "Procedure" not in falls_policies_df.columns or "Definition" not in falls_policies_df.columns:
        return jsonify({"Definition": "Error: Missing 'Procedure' or 'Definition' column in falls_policy.csv."})

    # Convert column to lowercase & strip spaces for consistent searching
    falls_policies_df["Procedure"] = falls_policies_df["Procedure"].astype(str).str.lower().str.strip()
    falls_policies_df["Definition"] = falls_policies_df["Definition"].astype(str).str.strip()

    procedure_lower = procedure.lower().strip()

    # Search for an exact or partial match
    match = falls_policies_df[falls_policies_df["Procedure"].str.contains(procedure_lower, case=False, na=False)]

    if not match.empty:
        definition = match.iloc[0]["Definition"]
        # Split the definition into bullet points
        sentence_list = re.split(r'(?<=[.!?])\s+', definition)
        # Format the output
        formatted_response = f"<strong>{procedure.capitalize()}:</strong><ul>"
        for sentence in sentence_list:
            if sentence.strip():
                formatted_response += f"<li>{sentence.strip()}</li>"
        formatted_response += "</ul>"

        return jsonify({"Definition": formatted_response})

    return jsonify({"Definition": "No definition found for the procedure."})


# Function to find the best answer for a given query
# I added a bit of formatting to make the response more readable
def find_best_answer(query):
    query_embedding = model.encode([query], convert_to_tensor=False, normalize_embeddings=True)
    query_embedding = np.array(query_embedding, dtype=np.float32)
    _, closest_index = faiss_index.search(query_embedding, 1)
    
    best_match = df.iloc[closest_index[0][0]]
    best_answer = best_match["Answer"]
    best_qtype = best_match["topic"]

    # these variables are used to create a unique ID for the hidden section
    unique_id = f"moreAnswers_{np.random.randint(10000, 99999)}"
    button_id = f"showMoreBtn_{np.random.randint(10000, 99999)}"

    # using HTML to format the response
    formatted_answer = f"""
    <div style="font-family: Arial, sans-serif; color: #333; padding: 15px; line-height: 1.8; background-color: #f8f9fa; border-radius: 10px; border: 1px solid #dcdcdc;">
        <h3 style="color: #1d68a7; margin-bottom: 10px; font-size: 1.25em;">📌 <strong>Category:</strong> <span style="color: #ff6600;">{best_qtype}</span></h3>
        <p><strong>Response:</strong></p>
        <ul id="answerList" style="background: #ffffff; padding: 15px; border-radius: 10px; border: 1px solid #dcdcdc; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
    """

    # splitting the answers based on punctuation marks for better readability
    answer_chunks = re.split(r'(?<=\.|\!|\?)\s+', best_answer)
    displayed_chunks = answer_chunks[:5]  # showing the first 5 responses

    # formatting the displayed answers
    for chunk in displayed_chunks:
        if len(chunk.strip()) > 0:
            formatted_answer += f"""
                <li style="margin-bottom: 10px; font-size: 1em; color: #333;">
                    <span style="color: #28a745;">✅</span> <span style="font-weight: 500;">{chunk.strip()}</span>
                </li>
            """
    
    # I made a hidden section for the remaining answers to avoid overflowing the webpage
    if len(answer_chunks) > 5:
        formatted_answer += f'<div id="{unique_id}" style="display: none; padding-top: 10px;">'
        for chunk in answer_chunks[5:]:
            if len(chunk.strip()) > 0:
                formatted_answer += f"""
                    <li style="margin-bottom: 10px; font-size: 1em; color: #333;">
                        <span style="color: #17a2b8;">🔹</span> {chunk.strip()}
                    </li>
                """
        formatted_answer += '</div>'

        # show more button
        formatted_answer += f"""
        <button id="{button_id}" onclick="document.getElementById('{unique_id}').style.display='block'; this.style.display='none';" 
        style="margin-top: 15px; padding: 8px 16px; background-color: #1d68a7; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">
            Show More ⬇
        </button>
        """

    formatted_answer += "</ul></div>"
    return formatted_answer

@app.route("/get_protocol", methods=["POST"])
def get_protocol():
    data = request.json
    query = data.get("query", "").strip()

    if not query:
        return jsonify({"answer": "Please ask a question.", "status": "error", "message": "No query provided."})

    best_answer = find_best_answer(query)
    return jsonify({"answer": best_answer, "status": "success", "message": best_answer})

if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
