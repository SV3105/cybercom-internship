// ---- state.js ----
const savedState = localStorage.getItem("appState");

const state = savedState
  ? JSON.parse(savedState)
  : {
      view: 'interns',
      loading: false,
      error: null,
       interns: [
        { id: 1, name: "Sneha Vaghela", email: "sneha@test.com", skills: ["JS","CSS"], status: "ONBOARDING" },
        { id: 2, name: "Rahul Sharma", email: "rahul@test.com", skills: ["HTML","CSS"], status: "ACTIVE" },
        { id: 3, name: "Anita Patel", email: "anita@test.com", skills: ["JS","React"], status: "ACTIVE" },
        { id: 4, name: "Vikram Singh", email: "vikram@test.com", skills: ["HTML","CSS","JS"], status: "EXITED" }
      ],
      tasks: [
        { id: 101, title: "Build Landing Page", requiredSkills: ["HTML","CSS"], status:"OPEN", dependencies:[], hours:5, assignedTo: 2 },
        { id: 102, title: "Create JS Form Validation", requiredSkills: ["JS"], status:"OPEN", dependencies:[101], hours:3, assignedTo: 3 },
        { id: 103, title: "Build React Component", requiredSkills: ["React","JS"], status:"OPEN", dependencies:[], hours:4, assignedTo:null },
        { id: 104, title: "CSS Animations", requiredSkills:["CSS"], status:"OPEN", dependencies:[], hours:2, assignedTo:null },
        { id: 105, title: "Final Testing", requiredSkills:["JS","HTML","CSS"], status:"OPEN", dependencies:[101,102,103,104], hours:6, assignedTo:null }
      ],
     logs: [
        { time: new Date().toLocaleTimeString(), message:"System initialized" }
      ],
      filters: {
        internStatus: "ALL",
        skill: "ALL",
      },
      sequence: 1
      
    };
function saveState() {
  localStorage.setItem("appState", JSON.stringify(state));
}

function setState(updater) {
  updater(state);
  saveState();
  render();
}

function logAction(message) {
  setState(state => {
    state.logs.push({
      message,
      time: new Date().toISOString()
    });
  });
}


function clearAppState() {
  localStorage.removeItem("appState");
  location.reload();
}