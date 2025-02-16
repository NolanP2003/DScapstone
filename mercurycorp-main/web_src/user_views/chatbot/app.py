"""
this file contains the code for the Flask application that serves the chatbot API
it uses the Sentence Transformer model to encode the questions and queries into 
vectors for similarity search. The chatbot API receives a query from the frontend
and returns the best answer from the dataset based on the similarity of the query
to the questions in the dataset.

The find_best_answer function takes a query as input, encodes the query using the 
Sentence Transformer model, and then uses a FAISS index to find the most similar
question in the dataset. It then returns the best answer along with the category
of the question.

The get_protocol route is the endpoint that receives the query from the frontend
and returns the best answer in JSON format. The response includes the best answer
formatted in HTML for better readability. The response also includes a status field
to indicate the success or failure of the request. The message field contains the
formatted answer. The app.run() method starts the Flask application on port 5000.
"""


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
