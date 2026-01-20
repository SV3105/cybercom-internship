// ---- Intern ID generator ----
function generateInternId() {
  const year = new Date().getFullYear();
  return `${year}-${state.sequence++}`;
}

// ---- Allowed transitions ----
function canTransition(from, to) {
  const rules = {
    ONBOARDING: ['ACTIVE'],
    ACTIVE: ['EXITED'],
    EXITED: []
  };
  return rules[from].includes(to);
}

// ---- Task creation ----
function createTask(data) {
  if (!data.title) throw new Error("Task title is required");

  const task = {
    id: Date.now(), // unique ID
    title: data.title,
    requiredSkills: data.requiredSkills || [],
    dependencies: data.dependencies || [],
    status: "OPEN",
    assignedTo: null,
    hours: data.hours || 0
  };

  if (hasCircularDependency(task, task.dependencies)) {
    throw new Error("Circular dependency detected");
  }

  setState(s => {
    s.tasks.push(task);
  });

  logAction(`Task created: ${task.title}`);
}

// ---- Circular dependency check ----
function hasCircularDependency(task, dependencies, visited = new Set()) {
  for (let depId of dependencies) {
    if (depId === task.id) return true;
    if (visited.has(depId)) continue;
    visited.add(depId);

    const depTask = state.tasks.find(t => t.id === depId);
    if (depTask && hasCircularDependency(task, depTask.dependencies, visited)) {
      return true;
    }
  }
  return false;
}

// ---- Assign task to intern ----
function assignTask(taskId, internId) {
  const intern = state.interns.find(i => i.id === internId);
  const task = state.tasks.find(t => t.id === taskId);

  if (!intern) throw new Error("Intern not found");
  if (intern.status !== 'ACTIVE') throw new Error('Only ACTIVE interns can receive tasks');
  if (task.assignedTo === internId) throw new Error('Task already assigned');

  setState(s => {
    task.assignedTo = internId;
  });

  logAction(`Task "${task.title}" assigned to ${intern.name}`);
}

// ---- Mark task done ----
function handleMarkTaskDone(taskId) {
  setState(s => {
    const task = s.tasks.find(t => t.id === taskId);
    if (!task) throw new Error("Task not found");

    const pendingDeps = task.dependencies.filter(depId => {
      const dep = s.tasks.find(t => t.id === depId);
      return dep && dep.status !== "DONE";
    });

    if (pendingDeps.length > 0) {
      throw new Error("Dependencies not completed");
    }

    task.status = "DONE";

    // auto-update READY tasks
    s.tasks.forEach(t => {
      if (t.status === "OPEN" && t.dependencies.length > 0) {
        const allDone = t.dependencies.every(depId => {
          const dep = s.tasks.find(d => d.id === depId);
          return dep && dep.status === "DONE";
        });
        if (allDone) {
          t.status = "READY";
          s.logs.push({
            time: new Date().toISOString(),
            message: `Task ready: ${t.title}`
          });
        }
      }
    });

    s.logs.push({
      time: new Date().toISOString(),
      message: `Task completed: ${task.title}`
    });
  });
}

// ---- Update intern status ----
function updateInternStatus(internId, newStatus) {
  const intern = state.interns.find(i => i.id === internId);
  if (!intern) return alert("Intern not found");

  if (intern.status === "EXITED" && newStatus === "ACTIVE") {
    return alert("Cannot activate an exited intern");
  }

  if (!canTransition(intern.status, newStatus)) {
    return alert(`Cannot change from ${intern.status} to ${newStatus}`);
  }

  setState(s => {
    intern.status = newStatus;
  });

  logAction(`Intern "${intern.name}" status changed to ${newStatus}`);
}
