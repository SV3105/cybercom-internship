const Validators = {
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    validateInternForm(formData) {
        const errors = [];
        
        if (!formData.name || formData.name.trim().length < 2) {
            errors.push('Name must be at least 2 characters');
        }
        
        if (!this.validateEmail(formData.email)) {
            errors.push('Invalid email format');
        }
        
        if (!formData.skills || formData.skills.length === 0) {
            errors.push('At least one skill is required');
        }
        
        return { valid: errors.length === 0, errors };
    },
    
    validateTaskForm(formData) {
        const errors = [];
        
        if (!formData.title || formData.title.trim().length < 3) {
            errors.push('Title must be at least 3 characters');
        }
        
        if (!formData.requiredSkills || formData.requiredSkills.length === 0) {
            errors.push('At least one skill is required');
        }
        
        if (!formData.estimatedHours || formData.estimatedHours < 1) {
            errors.push('Estimated hours must be at least 1');
        }
        
        if (formData.dependencies && formData.dependencies.length > 0) {
            const invalidDeps = formData.dependencies.filter(depId => 
                !State.tasks.find(t => t.id === depId)
            );
            
            if (invalidDeps.length > 0) {
                errors.push(`Invalid task IDs: ${invalidDeps.join(', ')}`);
            }
        }
        
        return { valid: errors.length === 0, errors };
    }
};