// DOM updates only - Manual rendering
const Renderer = {
    renderStats() {
        const activeInterns = State.interns.filter(i => i.status === 'ACTIVE').length;
        const totalHours = RulesEngine.calculateTotalHours();
        
        document.getElementById('stat-total').textContent = State.interns.length;
        document.getElementById('stat-active').textContent = activeInterns;
        document.getElementById('stat-tasks').textContent = State.tasks.length;
        document.getElementById('stat-hours').textContent = totalHours;
    },
    
    renderInternList() {
        const container = document.getElementById('intern-list');
        
        if (State.interns.length === 0) {
            container.innerHTML = '<p style="color: #999;">No interns yet. Create one above!</p>';
            return;
        }
        
        const html = `
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Skills</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${State.interns.map(intern => `
                        <tr>
                            <td>${intern.id}</td>
                            <td>${intern.name}</td>
                            <td>${intern.email}</td>
                            <td>${intern.skills.join(', ')}</td>
                            <td><span class="badge badge-${intern.status.toLowerCase()}">${intern.status}</span></td>
                            <td>
                                ${intern.status === 'ONBOARDING' ? 
                                    `<button class="btn action-btn" data-action="activate" data-id="${intern.id}">Activate</button>` : ''}
                                ${intern.status === 'ACTIVE' ? 
                                    `<button class="btn action-btn btn-secondary" data-action="exit" data-id="${intern.id}">Exit</button>` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
        
        // Attach event listeners
        container.querySelectorAll('button[data-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = e.target.dataset.action;
                const id = e.target.dataset.id;
                
                if (action === 'activate') {
                    App.activateIntern(id);
                } else if (action === 'exit') {
                    App.exitIntern(id);
                }
            });
        });
    },
    
    renderTaskList() {
        const container = document.getElementById('task-list');
        
        if (State.tasks.length === 0) {
            container.innerHTML = '<p style="color: #999;">No tasks yet. Create one above!</p>';
            return;
        }
        
        const activeInterns = State.interns.filter(i => i.status === 'ACTIVE');
        
        const html = `
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Skills</th>
                        <th>Hours</th>
                        <th>Dependencies</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${State.tasks.map(task => {
                        const assignedIntern = task.assignedTo ? 
                            State.interns.find(i => i.id === task.assignedTo) : null;
                        
                        return `
                            <tr>
                                <td>${task.id}</td>
                                <td>${task.title}</td>
                                <td>${task.requiredSkills.join(', ')}</td>
                                <td>${task.estimatedHours}h</td>
                                <td>${task.dependencies?.join(', ') || '-'}</td>
                                <td>${assignedIntern ? assignedIntern.name : 'Unassigned'}</td>
                                <td>${task.status || 'PENDING'}</td>
                                <td>
                                    ${!task.assignedTo ? `
                                        <select class="action-btn" style="padding: 5px;" data-task-id="${task.id}">
                                            <option value="">Assign to...</option>
                                            ${activeInterns.map(intern => {
                                                const canAssign = RulesEngine.canAssignTask(intern, task);
                                                return `<option value="${intern.id}" ${!canAssign.valid ? 'disabled' : ''}>
                                                    ${intern.name} ${!canAssign.valid ? '(N/A)' : ''}
                                                </option>`;
                                            }).join('')}
                                        </select>
                                    ` : ''}
                                    ${task.assignedTo && task.status !== 'DONE' ? 
                                        `<button class="btn action-btn" data-action="mark-done" data-task-id="${task.id}">Mark Done</button>` : ''}
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
        
        // Attach event listeners for assignment dropdowns
        container.querySelectorAll('select[data-task-id]').forEach(select => {
            select.addEventListener('change', (e) => {
                const taskId = e.target.dataset.taskId;
                const internId = e.target.value;
                
                if (internId) {
                    App.assignTask(taskId, internId);
                }
            });
        });
        
        // Attach event listeners for mark done buttons
        container.querySelectorAll('button[data-action="mark-done"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const taskId = e.target.dataset.taskId;
                App.markTaskDone(taskId);
            });
        });
    },
    
    render() {
        this.renderStats();
        this.renderInternList();
        this.renderTaskList();
    }
};