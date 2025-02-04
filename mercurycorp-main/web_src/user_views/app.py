from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import re

app = Flask(__name__)

data = pd.read_csv('medical_qa.csv')

questions = data['Question'].tolist()
answers = data['Answer'].tolist()

vectorizer = TfidfVectorizer(stop_words='english')
X = vectorizer.fit_transform(questions)

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

    query_vec = vectorizer.transform([query])

    cosine_similarities = cosine_similarity(query_vec, X)

    most_similar_index = cosine_similarities.argmax()

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
