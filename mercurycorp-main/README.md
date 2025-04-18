# mercurycorp

For our software engineering project, we propose developing a comprehensive database to digitize and securely store records that are currently maintained on paper, including electronic health records (EHR) and employee files. These records will be systematically organized to facilitate easy access and management. Our project will then expand into creating a software platform—a website or application—equipped with powerful search capabilities, categorization tools, and reporting features, serving as the system’s backend. Once this foundation is established, we will build modules to support Human Resources in employee needs and a dedicated interface for nurses. This nursing interface will not only enable quick retrieval of resident records but will also include a chatbot designed to assist with policy and procedure guidance, bias checking, and facility information. Additionally, the software will cater to the diverse needs of medical professionals by offering customizable options, such as language adjustments and accessibility features like a dyslexia-friendly setup. This tool will be especially valuable for agency personnel, enhancing their workflow and improving patient care.

Softwares similar are available (Point Click Care) however they do not support a variety of features and many instances, many applications will be needed to complete these tasks, These can often times not be user friendly and very expensive for facilities like non-profits. Our project will not only combine these in a simple interface, but also be open-source. 



## Requirements:
1. Make a Database filled with the patients, their information, and medical records. Also adding fake data to test if pulling database information will work.
2.  Having a simple website, that is clear and concise, bringing up user-information. Depending on who is logging in, there will be 3 different views, an employee view, a nurse view, and a patient view. These views will show different information that is relevant to that person (for example, an employer can see the nurse information, but not the information on the patients they treat. The nurse will have access to their patients information, but not HR's).
3.  Make a log in feature that showcases if person X logs in, they're able to see certain things.
4.  figuring out to integrate a PDF viewer onto the website to access medical records. 



## Updates for 2/10/25

#### Chatbot:
I changed up the chatbot so that it follows a MapReduce structure. I have not gotten it to work with MRJob yet, so I left it as is so that it provides a working example. I broke down that dataset a bit further and currently (offline without commits) have started trying to make an NLP model to better cipher through the data. Right now, it produces all the data that corresponds to the query, but in the future I want it better tailored towards the specific input. The formatting of the webpage is improved with additional CSS.



## Updates for 3/10/25

#### Chatbot:
I created a few csv files to store the data from Masonic Village. I had to manually create these files in a well-formatted way, so that was a bit time consuming. Then, I created buttons that tell the chatbot to either use general health data like before, and now the Masonic data for issues related to Masonic Village. This application will allow Masonic Village to utilize our product. When you click Masonic Health, you will be prompted with more buttons to select from one of three of the areas of procedures that they have. If you select fall management, a box will appear asking if you have a keyword that needs defined (fall management was the only one with keyword definitions). If you do, you can type the keyword and it will output the definition or what the abbreviation stands for. If you don't need a keyword, it will continue to wait for user input until answering with the definition of the procedure that you name. Going forward, I will do this same process for the other two areas so that in the end, you can get a simple output all procedures that Masonic Village follows.


## Updates for 3/31
I made the chatbot AI using an API key from OpenAI. For testing purchases, I added $5 into my account which is what's funding our chatbot's responses. I put the API key into a .env file which I have not uploaded to GitHub. This is for security reasons to prevent unauthorized people from accessing the API key. The logic is fairly straight forward and the chatbot now provides accurate and concise responses to the user's questions. The prompt to the AI can be changed at any time to give us flexibility in the future.
