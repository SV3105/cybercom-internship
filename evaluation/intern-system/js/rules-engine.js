// Business rules enforcement
const RulesEngine = {
    validateStatusTransition(currentStatus, newStatus) {
        const validTransitions = {
            'ONBOARDING': ['ACTIVE'],
            'ACTIVE': ['EXITED'],
            'EXITED': [] // Cannot transition from EXITED
        };
        
        return validTransitions[currentStatus]?.includes(newStatus) || false;
    },
    
    canAssignTask(intern, task) {
        // Must be ACTIVE
        if (intern.status !== 'ACTIVE') {
            return { valid: false, reason: 'Not Active' };
        }
        
        // Must have required skills
        const hasAllSkills = task.requiredSkills.every(skill => 
            intern.skills.includes(skill)
        );
        
        if (!hasAllSkills) {
            const missingSkills = task.requiredSkills.filter(skill => !intern.skills.includes(skill));
            return { valid: false, reason: `Missing: ${missingSkills.join(', ')}` };
        }
        
        // Check if already assigned
        if (task.assignedTo === intern.id) {
            return { valid: false, reason: 'Already assigned' };
        }
        
        return { valid: true };
    },
    
    detectCircularDependency(taskId, dependencies) {
        const visited = new Set();
        const recursionStack = new Set();
        
        const hasCycle = (currentId) => {
            if (recursionStack.has(currentId)) return true;
            if (visited.has(currentId)) return false;
            
            visited.add(currentId);
            recursionStack.add(currentId);
            
            const task = State.tasks.find(t => t.id === currentId);
            if (task && task.dependencies) {
                for (const depId of task.dependencies) {
                    if (hasCycle(depId)) return true;
                }
            }
            
            recursionStack.delete(currentId);
            return false;
        };
        
        // Check if adding these dependencies would create a cycle
        for (const depId of dependencies) {
            if (depId === taskId) return true; // Self-dependency
            if (hasCycle(depId)) return true;
        }
        
        return false;
    },
    
    canMarkTaskDone(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return { valid: true };
        }
        
        const unresolvedDeps = task.dependencies.filter(depId => {
            const depTask = State.tasks.find(t => t.id === depId);
            return !depTask || depTask.status !== 'DONE';
        });
        
        if (unresolvedDeps.length > 0) {
            return { 
                valid: false, 
                reason: `Unresolved dependencies: ${unresolvedDeps.join(', ')}` 
            };
        }
        
        return { valid: true };
    },
    
    calculateTotalHours() {
        return State.tasks.reduce((sum, task) => sum + (task.estimatedHours || 0), 0);
    },
    
    autoUpdateDependentTasks(completedTaskId) {
        // When a task is marked DONE, check if any tasks are now eligible
        State.tasks.forEach(task => {
            if (task.dependencies && task.dependencies.includes(completedTaskId)) {
                const allDepsResolved = task.dependencies.every(depId => {
                    const depTask = State.tasks.find(t => t.id === depId);
                    return depTask && depTask.status === 'DONE';
                });
                
                // Tasks auto-update status when dependencies are resolved
                if (allDepsResolved && task.status === 'PENDING') {
                    State.updateTask(task.id, { status: 'READY' });
                }
            }
        });
    }
};