const State = {
    interns: [],
    tasks: [],
    currentYear: new Date().getFullYear(),
    internSequence: 1,
    taskSequence: 1,
    
    getState() {
        return {
            interns: [...this.interns],
            tasks: [...this.tasks]
        };
    },
    
    addIntern(intern) {
        this.interns.push(intern);
        this.notify();
    },
    
    updateIntern(internId, updates) {
        const index = this.interns.findIndex(i => i.id === internId);
        if (index !== -1) {
            this.interns[index] = { ...this.interns[index], ...updates };
            this.notify();
        }
    },
    
    addTask(task) {
        this.tasks.push(task);
        this.notify();
    },
    
    updateTask(taskId, updates) {
        const index = this.tasks.findIndex(t => t.id === taskId);
        if (index !== -1) {
            this.tasks[index] = { ...this.tasks[index], ...updates };
            this.notify();
        }
    },
    
    getNextInternId() {
        const id = `${this.currentYear}${String(this.internSequence).padStart(3, '0')}`;
        this.internSequence++;
        return id;
    },
    
    getNextTaskId() {
        const id = `TASK-${String(this.taskSequence).padStart(3, '0')}`;
        this.taskSequence++;
        return id;
    },
    
    listeners: [],
    
    subscribe(listener) {
        this.listeners.push(listener);
    },
    
    notify() {
        this.listeners.forEach(listener => listener());
    }
};
