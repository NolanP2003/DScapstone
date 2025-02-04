# DScapstone


This research aims at creating an accurate prediction analysis model for the medical industry. Given a set of medical parameters, this model will be able to accurately predict the best course of action for the patient in a more streamlined and personalized way. Additionally, this project will incorporate a chatbot into the Mercury Corp. website, which will allow nurses to be able to quickly reference important procedures and protocols as needed. Using the chatbot in this way will help to serve as an valuable tool for training new employees, allowing them to quickly improve their own medical skills. Ultimately, this will contribute towards fewer misdiagnoses as well as improved decision making, leading to better outcomes for the patients.


### Chatbot
The chatbot is currently using the medical_qa.csv file. It is using a Flask app that separates the data into questions and answers. Then, it vectorizes the data and removes stop words. Using an extract_words function, it finds the important words for medical information based on a set list of key terms. Then, it handles query requests and returns a response. The web-page for the chatbot is still being created; however, this offers a start for the structure of the chatbot.
