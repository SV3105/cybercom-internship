const FakeServer = {
    async checkEmailUniqueness(email) {
        // Simulate network delay
        await this.delay(800);
        
        const exists = State.interns.some(intern => 
            intern.email.toLowerCase() === email.toLowerCase()
        );
        
        return { unique: !exists };
    },
    
    async createIntern(data) {
        await this.delay(500);
        return { success: true, data };
    },
    
    async createTask(data) {
        await this.delay(400);
        return { success: true, data };
    },
    
    async updateInternStatus(internId, newStatus) {
        await this.delay(300);
        return { success: true };
    },
    
    async assignTask(taskId, internId) {
        await this.delay(200);
        return { success: true };
    },
    
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
};
