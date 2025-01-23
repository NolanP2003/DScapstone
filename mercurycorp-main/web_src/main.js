//this is the javascript file for the website
//TODO

function switchUsername(){
    //TODO: when user log's in, say instead of "login" say "hi (name)"
}

function init(){
    for (let i = 0; i < elements.length; i++) {
        elements[i].addEventListener("mouseover", function () {
            elements[i].classList.add("active");
        });
        elements[i].addEventListener("mouseout", () => {
            elements[i].classList.remove("active");
        });
        elements[i].addEventListener("click", function () {
            let sections = qsa(".section-text");
            for (let i = 0; i < sections.length; i++) {
                sections[i].classList.add("hidden");
            }
            let list1 = elements[i].id.split("-"); //when you want to get the first item
            let sectionID = list1[0] + "-text"; //id becomes efd-text
            id(sectionID).classList.remove("hidden");
            let cateID = this.id.split("-")[0];
            showImages(cateID);
        })
    }
}

function toggleInfo(id) {
    const info = document.getElementById(id);
    if(infoDiv){
        infoDiv.style.display = infoDiv.style.display === 'none'? 'block' : 'none';
    }}

// function to set the view based on user status
function setUserView(status) {
    function setUserView(status) {
        const viewBox = document.getElementById("viewBox");
        const viewLabel = document.getElementById("viewLabel");
        const hrContent = document.getElementById("hrContent");
        const nurseContent = document.getElementById("nurseContent");
        const patientContent = document.getElementById("patientContent");
    
        // Reset classes and hide all sections
        viewBox.classList.remove("hr-view", "nurse-view", "patient-view");
        hrContent.style.display = "none";
        nurseContent.style.display = "none";
        patientContent.style.display = "none";
    
        // Set up the view based on user status
        if (status === "HR") {
            viewLabel.textContent = "HR View";
            viewBox.classList.add("hr-view");
            hrContent.style.display = "block";
        } else if (status === "Nurse") {
            viewLabel.textContent = "Nurse View";
            viewBox.classList.add("nurse-view");
            nurseContent.style.display = "block";
        } else if (status === "Patient") {
            viewLabel.textContent = "Patient View";
            viewBox.classList.add("patient-view");
            patientContent.style.display = "block";
        }
    }
}