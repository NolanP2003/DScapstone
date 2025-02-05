# Flask webserver that processes medical questions and returns protocols

from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import re

app = Flask(__name__)

# import data and set the columns for questions and answers
data = pd.read_csv('medical_qa.csv')
questions = data['Question'].tolist()
answers = data['Answer'].tolist()

# I imported TF-IDF vectorizer to transform the questions into vectors
# this is useful for finding the similarity between the nurse's question and the questions in the dataset
vectorizer = TfidfVectorizer(stop_words='english')
X = vectorizer.fit_transform(questions)

# the goal of this function is to extract the key information from the answer
# currently, it uses the important keywords list but the list is fairly small and may be inaccurate
# for future progress, I will use a more advanced method to extract the key information
def extract_key_info(answer):
    sentences = answer.split('.')
    important_keywords = ['treatment', 'medication', 'symptom', 'advice', 'therapy', 'precaution']
    important_info = []

    for sentence in sentences:
        if any(keyword in sentence.lower() for keyword in important_keywords):
            important_info.append(f"• {sentence.strip()}")

    if not important_info:
        important_info = [f"• {sentence.strip()}" for sentence in sentences]

    return important_info

# this is the main function that processes the nurse's question and returns the answer
# it creates a POST API endpoint that receives the nurse's question and returns the answer
@app.route('/get_protocol', methods=['POST'])
def get_protocol():
    data = request.json
    query = data.get('query', '')
    
    if not query:
        return jsonify({
            'answer': 'Please ask a question.',
            'status': 'error',
            'message': 'No query provided.'
        })

    # convert nurse query into TF-IDF vector
    query_vector = vectorizer.transform([query])
    
    # calculate cosine similarity between nurse query and dataset questions
    cosine_similarities = cosine_similarity(query_vector, X)
    
    # find the index of the most similar question
    most_similar_index = cosine_similarities.argmax()
    
    # get the answer for the most similar question
    best_answer = answers[most_similar_index]

    key_info = extract_key_info(best_answer)

    print(f"Query: {query}")
    print(f"Answer: {key_info}")

    return jsonify({
        'answer': key_info,
        'status': 'success',
        'message': 'Answer found successfully.'
    })

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
