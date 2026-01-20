// ---- Main render ----
let isRendering = false;

function render() {
  if (isRendering) return;
  isRendering = true;

  const app = document.getElementById("app");
  app.innerHTML = "";

  if (state.view === "interns") renderInterns(app);
  if (state.view === "tasks") renderTasks(app);
  if (state.view === "logs") renderLogs(app);

  isRendering = false;
}


// ---- Interns ----
function renderInterns(container) {
  const statusOptions = ["ALL", "ONBOARDING", "ACTIVE", "EXITED"];
  const skillOptions = ["ALL", "JS", "CSS", "HTML", "React"];

  const filtersHTML = `
    <div class="filters">
      <label>Status:
        <select id="filter-status">
          ${statusOptions.map(s => `<option value="${s}" ${state.filters.internStatus === s ? "selected" : ""}>${s}</option>`).join("")}
        </select>
      </label>
      <label>Skill:
        <select id="filter-skill">
          ${skillOptions.map(s => `<option value="${s}" ${state.filters.skill === s ? "selected" : ""}>${s}</option>`).join("")}
        </select>
      </label>
    </div>
  `;

  let interns = state.interns;
  if (state.filters.internStatus !== "ALL") {
    interns = interns.filter(i => i.status === state.filters.internStatus);
  }
  if (state.filters.skill !== "ALL") {
    interns = interns.filter(i => i.skills.includes(state.filters.skill));
  }

  const html = interns.map(i => {
    const assignedTasks = state.tasks.filter(t => t.assignedTo === i.id);
    const taskCount = assignedTasks.length;
    const totalHours = assignedTasks.reduce((sum, t) => sum + t.hours, 0);
    const statusColor = i.status === "ACTIVE" ? "green" : i.status === "ONBOARDING" ? "orange" : "gray";

    return `
      <div class="card" style="border-left:4px solid ${statusColor}">
        <b>${i.name}</b> (${i.status})<br/>
        Email: ${i.email}<br/>
        Skills: ${i.skills.join(", ")}<br/>
        Tasks assigned: ${taskCount} | Total hours: ${totalHours}
      </div>
    `;
  }).join("");

  const overallHours = interns.reduce((sum, i) => {
    const assignedTasks = state.tasks.filter(t => t.assignedTo === i.id);
    return sum + assignedTasks.reduce((s, t) => s + t.hours, 0);
  }, 0);

  container.innerHTML = `<h2>Interns</h2>${filtersHTML}${html}<p><b>Overall hours assigned:</b> ${overallHours}</p>`;

  document.getElementById("filter-status").onchange = e => {
     setState(state => {
    state.filters.internStatus = e.target.value;
  });
  render();
};
  document.getElementById("filter-skill").onchange = e => {
   setState(state => {
    state.filters.skill = e.target.value;
  });
  render();
};
}

// ---- Tasks ----
function renderTasks(container) {
  const html = state.tasks.map(t => {
    const assignedIntern = state.interns.find(i => i.id === t.assignedTo);
    const assignedName = assignedIntern ? assignedIntern.name : "Unassigned";
    const depTitles = t.dependencies.map(dId => {
      const dep = state.tasks.find(task => task.id === dId);
      return dep ? dep.title : "Unknown";
    });

    const statusColor = t.status === "DONE" ? "green" : t.status === "READY" ? "orange" : "blue";

    const canMarkDone = t.status === "READY" || (t.status === "OPEN" && t.dependencies.length === 0);
    const markBtnHTML = canMarkDone
      ? `<button class="mark-done-btn" data-task-id="${t.id}">Mark DONE</button>`
      : `<button disabled>Mark DONE</button>`;

    return `
      <div class="card" style="border-left:4px solid ${statusColor}">
        <b>${t.title}</b> - ${t.status}<br/>
        Assigned to: ${assignedName}<br/>
        Dependencies: ${depTitles.join(", ") || "None"}<br/>
        Hours: ${t.hours}<br/>
        ${markBtnHTML}
      </div>
    `;
  }).join("");

  container.innerHTML = `<h2>Tasks</h2>${html}`;

  document.querySelectorAll('.mark-done-btn').forEach(btn => {
    btn.onclick = () => {
      const taskId = parseInt(btn.dataset.taskId);
      try {
        handleMarkTaskDone(taskId);
      } catch(e) {
        alert(e.message);
      }
    };
  });
}

// ---- Logs ----
function renderLogs(container) {
  const logsHTML = state.logs.slice().reverse().map(l => `<p>${l.time} - ${l.message}</p>`).join("");
  container.innerHTML = `<h2>Audit Logs</h2><div class="logs-container">${logsHTML}</div>`;
}

