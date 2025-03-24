# DScapstone


This research aims at creating an accurate prediction analysis model for the medical industry. Given a set of medical parameters, this model will be able to accurately predict the best course of action for the patient in a more streamlined and personalized way. Additionally, this project will incorporate a chatbot into the Mercury Corp. website, which will allow nurses to be able to quickly reference important procedures and protocols as needed. Using the chatbot in this way will help to serve as an valuable tool for training new employees, allowing them to quickly improve their own medical skills. Ultimately, this will contribute towards fewer misdiagnoses as well as improved decision making, leading to better outcomes for the patients.


### Chatbot
The chatbot is currently using the medical_qa.csv file. It is using a Flask app that separates the data into questions and answers. Then, it vectorizes the data and removes stop words. Using an extract_words function, it finds the important words for medical information based on a set list of key terms. Then, it handles query requests and returns a response. The web-page for the chatbot is still being created; however, this offers a start for the structure of the chatbot.


### Patient Analysis
Git Commit 6: 

Modeling is trying to e fixed for the encoder_code. This target was changed due to high correlation with previous target. encode_code is a feature that uses SNOWMED CT codes which are the clinical finding codes used in medical records. This saves any issues that came from using ENCOUNTER_REASON. Models like RF, Logistic, LSTM, GRU, CNN were used. However, anything past RF could not be ran due to am issue in RAM from google colab. Will continue to try to make sure the models are memory efficient as well as running on a device that is more suited for the amount of memory usage. 

### Fixing Up Website
The website is currently being updated to improve upon some of its pages that look a bit rough around the edges. A new branch has also been created in order to work on the website from the branch before moving those new changes onto the main branch. That way, it would prevent errors from affecting the website in the main branch while also allowing for debugging to occur. Creation of a chatbot box on the nurse_dash.php page has been started in order to allow the nurses on the site to ask the chatbot questions if they need help. Chatbot can currently send responses back to user, but more work needs to be done in order to ensure no errors with chatbot code. Since there are certain responses to questions that the chatbot doesn't have any answers to, an automatic prompt is now given to let the user know that the chatbot can't answer the question in order to avoid the chatbot replying with an error.



### Git Commit 6:
Jackson and Nolan spent a lot of time this week making the website look more professional and structured. The overall user interface is improved with connections into the database to allow for accurate displays on the website. Employees who belong in Dept_ID 1 can now post announcements to the announcement board on the profile page. The tables and forms have been improved to make it as intuitive as possible for employees to fill out. As far as the chatbot goes, Jackson has improved the user interface a bit to make it easier to use. 
