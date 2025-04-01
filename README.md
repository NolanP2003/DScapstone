# DScapstone


This research aims at creating an accurate prediction analysis model for the medical industry. Given a set of medical parameters, this model will be able to accurately predict the best course of action for the patient in a more streamlined and personalized way. Additionally, this project will incorporate a chatbot into the Mercury Corp. website, which will allow nurses to be able to quickly reference important procedures and protocols as needed. Using the chatbot in this way will help to serve as an valuable tool for training new employees, allowing them to quickly improve their own medical skills. Ultimately, this will contribute towards fewer misdiagnoses as well as improved decision making, leading to better outcomes for the patients.


### Chatbot
The chatbot is currently using the medical_qa.csv file. It is using a Flask app that separates the data into questions and answers. Then, it vectorizes the data and removes stop words. Using an extract_words function, it finds the important words for medical information based on a set list of key terms. Then, it handles query requests and returns a response. The web-page for the chatbot is still being created; however, this offers a start for the structure of the chatbot.


### Patient Analysis
Git Commit 7: 

LSTM model was trained on medical records to predict patient outcomes. To maintain the temporal integrity and clinical accuracy of the data, I chose not to upsample minority classes, as this could distort the natural progression of patient histories and lead to unrealistic predictions, such as misclassifying flu as cancer. While the model achieved high training accuracy (~99%) compared to validation accuracy (~70%), this gap is expected due to the variability inherent in healthcare data. Despite some signs of overfitting, the decision to preserve the authenticity of medical records was prioritized. Future improvements could include implementing regularization or ensemble methods to enhance generalization without compromising data integrity. These methods are currently being reviewed and will be changed if any significant findings are present. The LOS model will be worked on and completed. 

### Fixing Up Website
The website is currently being updated to improve upon some of its pages that look a bit rough around the edges. A new branch has also been created in order to work on the website from the branch before moving those new changes onto the main branch. That way, it would prevent errors from affecting the website in the main branch while also allowing for debugging to occur. Creation of a chatbot box on the nurse_dash.php page has been started in order to allow the nurses on the site to ask the chatbot questions if they need help. Chatbot can currently send responses back to user, but more work needs to be done in order to ensure no errors with chatbot code. Since there are certain responses to questions that the chatbot doesn't have any answers to, an automatic prompt is now given to let the user know that the chatbot can't answer the question in order to avoid the chatbot replying with an error. Changed up the login page so that it redirects automatically to the nurse_dashboard page instead of the employee dashboard page. The resident dashboard page was also changed as the page wasn't configured to the style of the other mercury corp pages.



### Git Commit 6:
Jackson and Nolan spent a lot of time this week making the website look more professional and structured. The overall user interface is improved with connections into the database to allow for accurate displays on the website. Employees who belong in Dept_ID 1 can now post announcements to the announcement board on the profile page. The tables and forms have been improved to make it as intuitive as possible for employees to fill out. As far as the chatbot goes, Jackson has improved the user interface a bit to make it easier to use. 
