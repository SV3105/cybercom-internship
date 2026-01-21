# Frontend Intern Operations System

## Overview
A single-page application (SPA) that manages an intern lifecycle, task assignments, and skill-based workflows **entirely in JavaScript** without requiring a backend. This is a frontend-only system that demonstrates how frontend engineers can manage complex business logic, state management, and async workflows independently.

**Technology:** Vanilla JavaScript, HTML, CSS (No frameworks, no APIs)

---

## Features

### 1. **Application Shell**
- Single-page application with instant view switching
- No page reloads - all navigation happens in JavaScript
- Centralized loading and error handling
- Responsive design works on desktop, tablet, and mobile

### 2. **Central State Management**
- Single source of truth: `AppState` object
- No duplicated state across components
- Controlled updates through state setters
- Automatic re-rendering on state changes
- Audit trail of all actions (logs)

### 3. **Intern Management**
- Create interns with validation
- Auto-generated IDs (YYYY-SEQUENCE format)
- Async email uniqueness check (simulated)
- Intern lifecycle: ONBOARDING → ACTIVE → EXITED
- Cannot skip lifecycle steps (validation enforced)
- Cannot exit intern with incomplete tasks

### 4. **Skill-Based Task Assignment**
- Tasks require specific skills
- Assignments only to ACTIVE interns
- Automatic eligibility checking
- Intelligent recommendation system (assigns to least-busy intern)
- Skills influence task eligibility

### 5. **Task Management**
- Create tasks with required skills and estimated hours
- Track task status: TODO → IN_PROGRESS → DONE
- Prevent duplicate assignments
- Task status updates automatically when dependencies resolve

### 6. **Task Dependencies**
- Define task dependencies (what must be completed first)
- Circular dependency detection and prevention
- Tasks can only move to DONE when all dependencies are complete
- Dependent tasks are automatically marked as ready
- Visual display of dependency chains

### 7. **Validation & Error Handling**
- Form validation (name, email, skills, hours)
- Business rule validation (transitions, assignments)
- Async validation (email uniqueness)
- Graceful error recovery
- User-friendly error messages

### 8. **Role-Based Views** (Can be extended)
- Dashboard: Overview of system metrics
- Intern Management: Create, filter, manage interns
- Task Management: Create, assign, update tasks
- Audit Logs: Timestamped action history

### 9. **Logging & Audit Trail**
- All actions timestamped and logged
- Action types: INTERN_CREATED, INTERN_UPDATED, TASK_CREATED, TASK_STATUS_CHANGED, etc.
- View all logs with timestamps

---

## Architecture

### File Structure
```
intern-system/
├── index.html                 # Single HTML page
├── css/
│   ├── reset.css             # CSS reset
│   ├── layout.css            # Layout and structure
│   └── components.css        # Component styles
└── js/
    ├── state.js              # Central state management
    ├── validators.js         # Form & business rule validation
    ├── fake-server.js        # Async simulation (NO APIs)
    ├── rules-engine.js       # Business logic
    ├── renderer.js           # DOM updates
    └── app.js                # Bootstrap & event handling
```

### Module Responsibilities

#### **state.js** - Central State
- Single global `AppState` object
- Getters for all data
- Setters with logging
- No side effects

#### **validators.js** - Validation
- Email validation
- Name/skill validation
- Form validation
- Business rule validation
- Transition validation
- Circular dependency detection

#### **fake-server.js** - Async Simulation
- No real APIs - pure JavaScript
- `checkEmailUniqueness()` - async email check
- `createIntern()` - async intern creation
- `updateInternStatus()` - async status transition
- `createTask()` - async task creation
- `assignTaskToIntern()` - async assignment
- `updateTaskStatus()` - async status updates
- All operations use Promises

#### **rules-engine.js** - Business Logic
- `getEligibleInterns(taskId)` - who can do this task?
- `getEligibleTasks(internId)` - what can this intern do?
- `recommendTaskAssignment()` - intelligent matching
- `canExitIntern()` - is it safe to exit?
- `areDependenciesComplete()` - check status
- `getTotalEstimatedHours()` - calculate workload
- All read-only, no mutations

#### **renderer.js** - UI Updates
- `renderNavigation()` - top nav
- `renderUI()` - alerts/loading/errors
- `renderDashboard()` - dashboard view
- `renderInternsView()` - interns management
- `renderTasksView()` - tasks management
- `renderLogsView()` - action logs
- `renderForms()` - all forms
- Pure rendering, no logic

#### **app.js** - Bootstrap & Events
- `initializeApp()` - startup
- Event handlers for all user actions
- Form submission handling
- View switching
- Sample data initialization

---

## Key Constraints Implemented

### ✅ Intern ID Generation
```javascript
// Year + 4-digit sequence
2026-0001, 2026-0002, 2026-0003
```

### ✅ Email Uniqueness (Async)
- Checked asynchronously when creating intern
- Simulates backend email validation
- Prevents duplicates

### ✅ Status Transitions
```
ONBOARDING → ACTIVE    ✓ Valid
ACTIVE     → EXITED    ✓ Valid
EXITED     → ACTIVE    ✗ Invalid (blocked)
ACTIVE     → ONBOARDING ✗ Invalid (blocked)
```

### ✅ Task Dependencies
- Circular dependencies detected and blocked
- Tasks can't move to DONE with incomplete dependencies
- Dependent tasks auto-update when dependencies resolve

### ✅ Skill-Based Assignment
- Interns must have ALL required skills
- Only ACTIVE interns can be assigned tasks
- Prevents duplicate assignments

### ✅ Data Consistency
- Single state source - no duplication
- All updates through controlled setters
- Automatic re-render on any change
- No stale data across views

---

## Usage

### 1. Open in Browser
```bash
# Simply open index.html in any modern browser
# No build step required
# No server needed
open index.html
```

### 2. Create Interns
1. Go to **Interns** tab
2. Click **+ Add Intern**
3. Fill in name, email, and skills
4. System checks email uniqueness asynchronously
5. Intern created with auto-generated ID

### 3. Activate Interns
- Interns start in ONBOARDING status
- Click **Activate** to move to ACTIVE
- Only ACTIVE interns can be assigned tasks

### 4. Create Tasks
1. Go to **Tasks** tab
2. Click **+ Create Task**
3. Fill in name, description, required skills, hours
4. Optionally set dependencies (tasks that must complete first)
5. System detects circular dependencies

### 5. Assign Tasks
- Click **Assign** on a task
- System shows only eligible interns
- Shows recommended assignment (least busy)
- Task auto-starts (moves to IN_PROGRESS)

### 6. Update Task Status
- **Start**: TODO → IN_PROGRESS
- **Complete**: IN_PROGRESS → DONE (only if dependencies are done)
- **Reset**: IN_PROGRESS → TODO

### 7. Exit Interns
- Click **Exit** on an ACTIVE intern
- System prevents exit if tasks are incomplete
- Must complete or reassign all tasks first

### 8. View Dashboard
- See overall metrics
- Task and intern distribution
- Skill proficiency across team

### 9. Review Logs
- Timestamped action history
- See what changed and when

---

## Business Rules Enforced

### Intern Lifecycle
- ✓ Cannot skip lifecycle steps
- ✓ Cannot transition backwards (EXITED → ACTIVE forbidden)
- ✓ Cannot exit with incomplete tasks
- ✓ ONBOARDING → ACTIVE requires explicit activation

### Task Management
- ✓ Task requires at least one skill
- ✓ Estimated hours: 1-200
- ✓ Tasks can't be assigned twice
- ✓ Only ACTIVE interns can be assigned

### Dependencies
- ✓ Circular dependencies blocked
- ✓ Task can't move to DONE with incomplete dependencies
- ✓ Dependent tasks auto-update when dependency completes

### Validation
- ✓ Email format validation
- ✓ Email uniqueness (async check)
- ✓ Name length validation
- ✓ At least one skill required
- ✓ Form field required validation

---

## Sample Data

The app loads with sample data:
- 3 sample interns (1 ONBOARDING, 2 ACTIVE)
- 5 sample tasks with various statuses
- Dependencies between tasks
- Ready to use immediately

---

## Async Simulation

**No real APIs are used.** All async behavior is simulated:

```javascript
// Example: Email uniqueness check
FakeServer.checkEmailUniqueness(email)
  .then(result => { /* unique */ })
  .catch(error => { /* not unique */ })

// Uses Promise with setTimeout to simulate network delay
// Checks against actual state in memory
```

All async operations:
- Have artificial delays (300-500ms)
- Use Promises
- Can fail and throw errors
- Are handled with loading states and error messages

---

## State Structure

```javascript
AppState.state = {
  interns: [
    {
      id: "2026-0001",
      name: "Alice Johnson",
      email: "alice@example.com",
      skills: ["JavaScript", "React"],
      status: "ACTIVE",
      createdAt: "...",
      updatedAt: "..."
    }
  ],
  
  tasks: [
    {
      id: "TASK-001",
      name: "Build UI",
      description: "...",
      requiredSkills: ["React"],
      estimatedHours: 20,
      status: "IN_PROGRESS",
      assignedTo: "2026-0001",
      dependencies: ["TASK-002"],
      createdAt: "...",
      updatedAt: "..."
    }
  ],
  
  logs: [
    {
      timestamp: "2026-01-20T10:30:00Z",
      action: "INTERN_CREATED",
      description: "Intern Alice Johnson created with ID 2026-0001",
      internId: "2026-0001"
    }
  ],
  
  currentView: "dashboard",
  selectedInternId: null,
  filters: {
    internStatus: "ALL",
    internSkills: []
  },
  ui: {
    loading: false,
    error: null,
    successMessage: null
  }
}
```

---

## Performance Considerations

- Entire state in memory (fast)
- No API latency (instant execution except simulated delay)
- Efficient filtering and searching
- No unnecessary re-renders (single render call)
- Pure functions for business logic

---

## Browser Compatibility

- Modern browsers only (ES6+)
- Chrome, Firefox, Safari, Edge
- No Internet Explorer
- Requires JavaScript enabled

---

## Development Notes

### Adding New Features
1. Add validation to `validators.js`
2. Add business logic to `rules-engine.js`
3. Add async simulation to `fake-server.js`
4. Update `AppState` in `state.js` if new state needed
5. Add UI in `renderer.js`
6. Add event handlers in `app.js`

### Extending the System
- Add user authentication (store in state)
- Add more roles (Admin, Manager, Intern views)
- Add more complex workflows
- Add data export/import
- Add local storage persistence

### Testing
- No unit tests (frontend-only requirement)
- Test manually through UI
- Check browser console for any errors
- Verify state in browser DevTools

---

## Key Takeaways

This system demonstrates:
1. **Frontend Autonomy**: Complex business logic without backend
2. **State Management**: Single source of truth pattern
3. **Async Handling**: Promises without real APIs
4. **Validation**: Multi-layer validation (form, business, async)
5. **UI/Logic Separation**: Clean separation of concerns
6. **Dependency Management**: Complex dependency resolution
7. **Error Handling**: Graceful recovery from failures
8. **Data Consistency**: No duplication, no stale data

---

## Submission Checklist

- ✅ Pure JavaScript (no frameworks)
- ✅ HTML only (1 page)
- ✅ CSS only (no preprocessors)
- ✅ No backend or APIs
- ✅ State management pattern
- ✅ Async simulation with Promises
- ✅ Validation & error handling
- ✅ Task dependencies & circular detection
- ✅ Role-based views
- ✅ Audit logging
- ✅ Responsive design
- ✅ Sample data included
- ✅ README documentation

---

## Author
Created as a Frontend Intern Technical Assessment

**Date:** January 20, 2026  
**Duration:** ~4-5 hours development  
**Technology Stack:** JavaScript, HTML, CSS, Git
