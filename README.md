# DScapstone


This research aims at creating an accurate prediction analysis model for the medical industry. Given a set of medical parameters, this model will be able to accurately predict the best course of action for the patient in a more streamlined and personalized way. Additionally, this project will incorporate a chatbot into the Mercury Corp. website, which will allow nurses to be able to quickly reference important procedures and protocols as needed. Using the chatbot in this way will help to serve as an valuable tool for training new employees, allowing them to quickly improve their own medical skills. Ultimately, this will contribute towards fewer misdiagnoses as well as improved decision making, leading to better outcomes for the patients.


### Chatbot
The chatbot is currently using the medical_qa.csv file. It is using a Flask app that separates the data into questions and answers. Then, it vectorizes the data and removes stop words. Using an extract_words function, it finds the important words for medical information based on a set list of key terms. Then, it handles query requests and returns a response. The web-page for the chatbot is still being created; however, this offers a start for the structure of the chatbot.


### Patient Analysis
Git Commit 4: 
A machine learning model to predict patient encounters was developed and optimized based on their medical history. After working with many models, including LSTM, GRU, and CNN, it was  found that LSTM with focal loss and hyperparameter tuning provided the best balance between accuracy and handling class imbalances. The final LSTM model achieved 94.3% accuracy, performing well on frequent medical encounters like general exams and check-ups but struggling with rare and critical cases such as emergency admissions, vaccinations, and death certifications. 

To address these limitations the following are future changes that will be made:
- Improve class balance (upsampling rare encounters, adjusting loss dynamically).  
- Introduce more features (condition severity, medications, procedures).  
- Experiment with new models (BiLSTM, Attention, Transformers).  
- Begin development of LOS prediction model.

Additionally, an outlined approach for predicting Length of Stay (LOS) was made. Potential models for LOS prediction include  Gradient Boosting for explainability and LSTM/GRU for capturing long-term trends.


### Fixing Up Website
The website is currently being updated to improve upon some of its pages that look a bit rough around the edges. A new branch has also been created in order to work on the website from the branch before moving those new changes onto the main branch. That way, it would prevent errors from affecting the website in the main branch while also allowing for debugging to occur. Creation of a chatbot box on the nurse_dash.php page has been started in order to allow the nurses on the site to ask the chatbot questions if they need help. Chatbot can currently send responses back to user, but more work needs to be done in order to ensure no errors with chatbot code. Since there are certain responses to questions that the chatbot doesn't have any answers to, an automatic prompt is now given to let the user know that the chatbot can't answer the question in order to avoid the chatbot replying with an error.
