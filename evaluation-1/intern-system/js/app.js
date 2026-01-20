document.querySelectorAll("nav button").forEach(btn => {
  btn.addEventListener("click", () => {
    setState(state => {
    state.view = btn.dataset.view;
  });

  });
});



// Expose markTaskDone to global scope for buttons
window.handleMarkTaskDone = function(taskId) {
  try {
    markTaskDone(taskId);
  } catch (err) {
    console.error(err.message);
  }
};




async function createIntern(data) {
  // 1. Basic validation (sync)
  validateIntern(data);

  // 2. Async email uniqueness check (fake backend)
  const isUnique = await checkEmailUnique(data.email);
  if (!isUnique) {
    throw new Error("Email already exists");
  }

  // 3. Create intern object (business rules)
  const intern = {
    id: generateInternId(),
    name: data.name,
    email: data.email,
    skills: data.skills || [],
    status: "ONBOARDING"
  };

  // 4. Update centralized state
  setState(state => {
    state.interns.push(intern);
  });

  // 5. Audit log
  logAction(`Intern created: ${intern.name}`);
}

function assignTaskToIntern(taskId, internId) {
  setState(state => {
    const task = state.tasks.find(t => t.id === taskId);
    const intern = state.interns.find(i => i.id === internId);

    if (!task || !intern) {
      alert("Invalid task or intern");
      return;
    }

    if (intern.status !== "ACTIVE") {
      alert("Only ACTIVE interns can be assigned tasks");
      return;
    }

    if (task.assignedTo) {
      alert("Task already assigned");
      return;
    }

    const hasAllSkills = task.requiredSkills.every(skill =>
      intern.skills.includes(skill)
    );

    if (!hasAllSkills) {
      alert("Intern does not have required skills");
      return;
    }

    task.assignedTo = intern.id;
    state.logs.push({
      time: new Date().toISOString(),
      message: `Task "${task.title}" assigned to ${intern.name}`
    });
  });
}

render();
