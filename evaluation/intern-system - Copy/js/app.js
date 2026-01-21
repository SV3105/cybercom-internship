// Bootstrap & event wiring
const App = {
    init() {
        console.log('🚀 Initializing Intern Operations System...');
        
        // Subscribe to state changes
        State.subscribe(() => Renderer.render());
        
        // Wire up forms
        const internForm = document.getElementById('intern-form');
        const taskForm = document.getElementById('task-form');
        
        if (internForm) {
            internForm.addEventListener('submit', this.handleInternSubmit.bind(this));
            console.log('✓ Intern form listener attached');
        }
        
        if (taskForm) {
            taskForm.addEventListener('submit', this.handleTaskSubmit.bind(this));
            console.log('✓ Task form listener attached');
        }
        
        // Initial render
        Renderer.render();
        
        console.log('✅ System ready!');
    },
    
    async handleInternSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorDiv = document.getElementById('email-error');
        
        const formData = {
            name: document.getElementById('intern-name').value.trim(),
            email: document.getElementById('intern-email').value.trim(),
            skills: Array.from(document.querySelectorAll('input[name="skill"]:checked'))
                .map(cb => cb.value)
        };
        
        // Validate form
        const validation = Validators.validateInternForm(formData);
        if (!validation.valid) {
            errorDiv.textContent = validation.errors.join(', ');
            errorDiv.className = 'error';
            return;
        }
        
        // Check email uniqueness (async)
        submitBtn.disabled = true;
        submitBtn.textContent = 'Checking email...';
        errorDiv.textContent = '';
        
        const emailCheck = await FakeServer.checkEmailUniqueness(formData.email);
        
        if (!emailCheck.unique) {
            errorDiv.textContent = 'Email already exists!';
            errorDiv.className = 'error';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Intern';
            return;
        }
        
        submitBtn.textContent = 'Creating...';
        
        // Create intern
        const intern = {
            id: State.getNextInternId(),
            name: formData.name,
            email: formData.email,
            skills: formData.skills,
            status: 'ONBOARDING',
            createdAt: new Date().toISOString()
        };
        
        await FakeServer.createIntern(intern);
        State.addIntern(intern);
        
        // Reset form
        form.reset();
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Intern';
        errorDiv.className = 'success';
        errorDiv.textContent = `✓ Intern ${intern.id} created successfully!`;
        setTimeout(() => errorDiv.textContent = '', 3000);
    },
    
    async handleTaskSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorDiv = document.getElementById('dependency-error');
        
        const depsInput = document.getElementById('task-dependencies').value.trim();
        const dependencies = depsInput ? 
            depsInput.split(',').map(d => d.trim()).filter(d => d) : [];
        
        const formData = {
            title: document.getElementById('task-title').value.trim(),
            description: document.getElementById('task-description').value.trim(),
            requiredSkills: Array.from(document.querySelectorAll('input[name="task-skill"]:checked'))
                .map(cb => cb.value),
            estimatedHours: parseInt(document.getElementById('task-hours').value),
            dependencies
        };
        
        // Validate form
        const validation = Validators.validateTaskForm(formData);
        if (!validation.valid) {
            errorDiv.textContent = validation.errors.join(', ');
            errorDiv.className = 'error';
            return;
        }
        
        const taskId = State.getNextTaskId();
        
        // Check circular dependencies
        if (RulesEngine.detectCircularDependency(taskId, dependencies)) {
            errorDiv.textContent = 'Circular dependency detected!';
            errorDiv.className = 'error';
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';
        errorDiv.textContent = '';
        
        // Create task
        const task = {
            id: taskId,
            title: formData.title,
            description: formData.description,
            requiredSkills: formData.requiredSkills,
            estimatedHours: formData.estimatedHours,
            dependencies: dependencies.length > 0 ? dependencies : null,
            status: 'PENDING',
            assignedTo: null,
            createdAt: new Date().toISOString()
        };
        
        await FakeServer.createTask(task);
        State.addTask(task);
        
        // Reset form
        form.reset();
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Task';
        errorDiv.className = 'success';
        errorDiv.textContent = `✓ Task ${task.id} created successfully!`;
        setTimeout(() => errorDiv.textContent = '', 3000);
    },
    
    async activateIntern(internId) {
        const intern = State.interns.find(i => i.id === internId);
        if (!intern) return;
        
        if (!RulesEngine.validateStatusTransition(intern.status, 'ACTIVE')) {
            alert('Invalid status transition!');
            return;
        }
        
        await FakeServer.updateInternStatus(internId, 'ACTIVE');
        State.updateIntern(internId, { status: 'ACTIVE' });
    },
    
    async exitIntern(internId) {
        const intern = State.interns.find(i => i.id === internId);
        if (!intern) return;
        
        if (!RulesEngine.validateStatusTransition(intern.status, 'EXITED')) {
            alert('Invalid status transition!');
            return;
        }
        
        if (confirm(`Are you sure you want to exit ${intern.name}? This cannot be undone.`)) {
            await FakeServer.updateInternStatus(internId, 'EXITED');
            State.updateIntern(internId, { status: 'EXITED' });
        }
    },
    
    async assignTask(taskId, internId) {
        if (!internId) return;
        
        const task = State.tasks.find(t => t.id === taskId);
        const intern = State.interns.find(i => i.id === internId);
        
        if (!task || !intern) return;
        
        const canAssign = RulesEngine.canAssignTask(intern, task);
        if (!canAssign.valid) {
            alert(canAssign.reason);
            return;
        }
        
        await FakeServer.assignTask(taskId, internId);
        State.updateTask(taskId, { assignedTo: internId, status: 'IN_PROGRESS' });
    },
    
    async markTaskDone(taskId) {
        const task = State.tasks.find(t => t.id === taskId);
        if (!task) return;
        
        const canMarkDone = RulesEngine.canMarkTaskDone(task);
        if (!canMarkDone.valid) {
            alert(canMarkDone.reason);
            return;
        }
        
        State.updateTask(taskId, { status: 'DONE' });
        RulesEngine.autoUpdateDependentTasks(taskId);
    }
};

// Initialize app on load
window.addEventListener('DOMContentLoaded', () => {
    App.init();
});