# DScapstone


This research aims at creating an accurate prediction analysis model for the medical industry. Given a set of medical parameters, this model will be able to accurately predict the best course of action for the patient in a more streamlined and personalized way. Additionally, this project will incorporate a chatbot into the Mercury Corp. website, which will allow nurses to be able to quickly reference important procedures and protocols as needed. Using the chatbot in this way will help to serve as an valuable tool for training new employees, allowing them to quickly improve their own medical skills. Ultimately, this will contribute towards fewer misdiagnoses as well as improved decision making, leading to better outcomes for the patients.


### Chatbot
The chatbot is currently using the medical_qa.csv file. It is using a Flask app that separates the data into questions and answers. Then, it vectorizes the data and removes stop words. Using an extract_words function, it finds the important words for medical information based on a set list of key terms. Then, it handles query requests and returns a response. The web-page for the chatbot is still being created; however, this offers a start for the structure of the chatbot.


### Patient Analysis
#### Length of Stay Prediction Model

This model is designed to predict a patient’s length of hospital stay, expressed in whole days. It leverages key patient data to provide accurate and interpretable output that can support healthcare planning and operational efficiency.

Input Features:
- Unique patient identifier  
- Demographic details
- Medical history, including prior diagnoses, comorbidities, and encounter types  

Processing Workflow:
1. Data Preprocessing: The input data undergoes cleaning and validation to handle missing values and ensure consistency.
2. Feature Engineering:  
   - Categorical variables are encoded appropriately.  
   - Time-aware features such as days since last visit and recency scores are generated.  
   - Temporal shifting is applied to align the data properly for sequence-based prediction.
3. Model Input: The final processed data is then passed into the trained machine learning model.
4. Output: The model returns a predicted numerical length of stay, rounded to the nearest whole day for practical use.

This model can help hospitals better allocate resources, anticipate discharge planning, and support overall patient flow management.

After tuning multiple regression models, an ensemble of the best-performing Lasso and ElasticNet configurations was selected for predicting patient length of stay. Among individual models, Lasso with an alpha of 0.01 performed best on the test set with a Mean Absolute Error (MAE) of 0.0693 and Root Mean Squared Error (RMSE) of 0.0940. Similarly, ElasticNet with alpha = 0.01 and l1_ratio = 0.5 achieved a test MAE of 0.0622 and RMSE of 0.0890. When combined, the ensemble model yielded the strongest overall performance, achieving a test MAE of 0.0657 and RMSE of 0.0914, indicating improved accuracy and generalization. These results suggest that the ensemble approach successfully leverages the strengths of both models, resulting in more stable and reliable predictions of hospital length of stay.

#### Encounter Reason Classification Model

This model predicts the likely reason for a patient’s next healthcare encounter, aiding in proactive care planning and resource allocation. It categorizes the visit into one of five encounter types: routine, chronic, urgent, acute, or rare.

Input Features:
- Unique patient identifier  
- Demographic information 
- Insurance coverage type  
- Medical procedure and diagnosis codes  
- History of the past five patient encounters  

Processing Workflow:
1. Data Preprocessing: Initial cleanup ensures all required fields are available, and any missing or malformed data is handled.
2. Feature Engineering:  
   - Categorical fields are encoded (e.g., insurance type, procedure codes).  
   - Encounter types are frequency encoded to capture their relevance in the dataset.  
   - Time-based features (e.g., days since last visit) are computed and shifted for temporal modeling.  
   - All numerical values are standardized for consistency.
3. Model Input: Processed features are passed into the classification model trained to recognize patterns across patient timelines.
4. Output: The model returns a predicted class label corresponding to the expected type of the next encounter: routine, chronic, urgent, acute, or rare.

This model supports clinicians and healthcare staff by anticipating patient needs and optimizing care delivery pathways.

The best-performing model for encounter reason classification was a LSTM network. While it demonstrated an overall accuracy (25%), its performance varied significantly across classes. The model performed best on the routine class, with a recall of 0.51 and an F1-score of 0.33, indicating it was relatively effective at identifying common, non-urgent visits. However, it struggled with underrepresented classes such as acute, rare, and urgentcare, where it failed to make any correct predictions, resulting in precision and recall scores of 0.00 for those categories. The macro and weighted averages reflect this imbalance, with an F1-score of 0.11 (macro) and 0.21 (weighted). These results highlight a need for more specific data. If the model were trained on data tailored to a specific population, such as geriatric or pediatric patients, it may have performed better. Additionally, applying class balancing techniques could improve performance across underrepresented encounter types, however this was avoided to an extreme extent to keep the data consistent. 

### Fixing Up Website
The website is currently being updated to improve upon some of its pages that look a bit rough around the edges. A new branch has also been created in order to work on the website from the branch before moving those new changes onto the main branch. That way, it would prevent errors from affecting the website in the main branch while also allowing for debugging to occur. Creation of a chatbot box on the nurse_dash.php page has been started in order to allow the nurses on the site to ask the chatbot questions if they need help. Chatbot can currently send responses back to user, but more work needs to be done in order to ensure no errors with chatbot code. Since there are certain responses to questions that the chatbot doesn't have any answers to, an automatic prompt is now given to let the user know that the chatbot can't answer the question in order to avoid the chatbot replying with an error. Changed up the login page so that it redirects automatically to the nurse_dashboard page instead of the employee dashboard page. The resident dashboard page was also changed as the page wasn't configured to the style of the other mercury corp pages.



### Git Commit 6:
Jackson and Nolan spent a lot of time this week making the website look more professional and structured. The overall user interface is improved with connections into the database to allow for accurate displays on the website. Employees who belong in Dept_ID 1 can now post announcements to the announcement board on the profile page. The tables and forms have been improved to make it as intuitive as possible for employees to fill out. As far as the chatbot goes, Jackson has improved the user interface a bit to make it easier to use. 


# Milestone 3

### Chatbot:
The chatbot now utilizes an OpenAI key that allows us to use artificial intelligence to generate accurate and concise answers to the user's query. The prompt that we gave the chatbot is listed in app.py file. AI is given the prompt which allows it to understand the type of response we are looking for. Responses are given in bulleted format. After each response, the chatbot will ask the user if they need another question answered and to re-select from the two options: General Health or Masonic Policies. The structure of the masonicDocs folder has changed significantly. It is now comprised of a single CSV file that contains all of the policies given to us by Masonic Village. This change was done to improve the speed of iteration for the chatbot, and to reduce the amount of selection buttons for users to select. Now, they can simply choose Masonic Policies and type the procedure or keyword that they need defined. This made it much easier for us to output responses without losing track of file paths. In the future, we expect to completely remove the chatbotData folder as it is now unnecessary for the success of our chatbot. The user interface of the chatbot has improved. It now allows users to drag the window wherever they want on the screen. They can also minimize the window or completely close it. 

#### Usage of the chatbot:
To use the chatbot, you first have to download the full code from our Git Repository. Once you have the code, in a terminal you must cd into the chatbot folder. Unless you have access to our API key, you will not be able to use our chatbot from just this code alone. You would have to generate a unique API key on OpenAI's website and add a minimum of $5 worth of credits into your account. This will fund the responses to your queries. Once you have an API key created, make a file named ".env" and put this file in the chatbot folder. In this file, type: OPENAI_API_KEY="YOUR_API_KEY_HERE". Once you have that, go back to your terminal where you are in the chatbot folder. Type "python app.py" and it will activate the chatbot. You may have to download some libraries depending on what you already have installed.

### Interface of the website:
The website has become more user-friendly overall. When you log in, an animation of our Mercury logo appears on the page adding a unique flare to the website. The forms that an employee would fill out for patients are cleaned up, making it much easier to decide what to do at each box. Employees with the nurse ID can make announcements that appear for every employee. This can be changed in the future depending on the different ID's given to employees. You can now add residents on the add_resident.php page. On this page, you fill out entries on a form and it gets sent to the database. For announcements, we added an independent table to the database that stores the announcements, allowing us to display them on the webpage.
