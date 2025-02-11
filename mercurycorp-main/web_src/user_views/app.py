from flask import Flask, request, jsonify
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import re

app = Flask(__name__)

data = pd.read_csv('medical_qa.csv')
questions = data['Question'].tolist()
answers = data['Answer'].tolist()
qtypes = data['qtype'].tolist()

# TfidfVectorizer to convert the questions into vectors
vectorizer = TfidfVectorizer(stop_words='english')
X = vectorizer.fit_transform(questions)

# Attempting mapreduce in the API
def map_query_to_answer(query):
    query_vector = vectorizer.transform([query])
    cosine_similarities = cosine_similarity(query_vector, X)
    
    return cosine_similarities

def reduce_to_best_answer(cosine_similarities, query):
    most_similar_index = cosine_similarities.argmax()
    best_answer = answers[most_similar_index]
    best_qtype = qtypes[most_similar_index]
    
    formatted_answer = f"<h3><strong>Response for '{query}':</strong></h3>"
    formatted_answer += f"<p><strong>Category:</strong> {best_qtype}</p>"
    
    formatted_answer += "<ul>"

    answer_chunks = re.split(r'(?<=\.|\!|\?)\s+', best_answer)
    for chunk in answer_chunks:
        formatted_answer += f"<li>{chunk.strip()}</li>"
    
    formatted_answer += "</ul>"

    return formatted_answer

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

    cosine_similarities = map_query_to_answer(query)
    best_answer = reduce_to_best_answer(cosine_similarities, query)

    return jsonify({
        'answer': best_answer,
        'status': 'success',
        'message': best_answer
    })

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)

