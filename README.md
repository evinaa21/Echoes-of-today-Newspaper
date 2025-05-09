# Echoes of Today - Online Newspaper

## Project Overview

Echoes of Today is a PHP and MySQL-based online newspaper platform.

## Getting Started with Git & GitHub (Using GUI Tools)

This guide helps you use GitHub Desktop or Visual Studio Code for managing the project's source code.

**1. Cloning the Repository (Getting the Project Files):**

This copies the project from GitHub to your computer.

- **Using GitHub Desktop:**

  1.  Open GitHub Desktop.
  2.  Go to `File > Clone Repository`.
  3.  Select the "URL" tab.
  4.  Paste the repository URL (provided by your team lead, e.g., `https://github.com/your-team/Echoes-of-today-Newspaper.git`).
  5.  For "Local path", choose your XAMPP `htdocs` directory (e.g., `c:\xampp\htdocs\`). GitHub Desktop will create the `Echoes-of-today-Newspaper` folder inside it.
  6.  Click **"Clone"**.

- **Using VS Code:**
  1.  Open VS Code.
  2.  Open the Command Palette (`Ctrl+Shift+P` or `Cmd+Shift+P`).
  3.  Type `Git: Clone` and select it.
  4.  Paste the repository URL and press Enter.
  5.  Select a local directory to save the project (your XAMPP `htdocs` folder, e.g., `c:\xampp\htdocs\`). VS Code will create the project folder there.
  6.  Once cloned, VS Code will ask if you want to open the cloned repository. Click "Open".

**You now have the project files! Proceed to Database Setup (see relevant section below).**

---

## Basic Git Workflow for Team Collaboration (GUI Tools)

**A. Getting Updates (Pulling Changes):**
Always get the latest changes from GitHub before starting work.

- **GitHub Desktop:**
  1.  Select the repository.
  2.  Ensure the "Current Branch" is `main` (or your team's primary branch).
  3.  Click **"Fetch origin"**. If there are new changes, it will show **"Pull origin"**. Click it.
- **VS Code:**
  1.  Open the Source Control view (branch icon).
  2.  Ensure you're on the `main` branch.
  3.  Click the "..." (More Actions) menu, then **"Pull"**. Or use the "Synchronize Changes" button in the status bar.

**B. Working on Features/Fixes (Branching):**
Create a new branch for your work to keep it separate from `main`.

- **GitHub Desktop:**
  1.  Click "Current Branch" > "New Branch".
  2.  Name it (e.g., `feature/user-login`) and base it on `main`.
  3.  Click "Create Branch", then "Publish branch".
- **VS Code:**
  1.  Click the branch name in the bottom-left status bar.
  2.  Select "+ Create new branch...". Name it.
  3.  Publish by clicking "..." in Source Control > "Publish Branch".

**C. Saving Your Work (Committing):**
Save your changes locally with a descriptive message.

- **GitHub Desktop:**
  1.  Changed files appear in the "Changes" tab. Check the ones to include.
  2.  Write a "Summary" (commit message) and click "Commit to `your-branch-name`".
- **VS Code:**
  1.  In Source Control, stage changes by clicking "+" next to files.
  2.  Type a message in the "Message" box and click the checkmark (Commit).

**D. Sharing Your Work (Pushing):**
Upload your committed changes to GitHub.

- **GitHub Desktop:** Click **"Push origin"**.
- **VS Code:** Click the "Synchronize Changes" button (status bar) or "..." > "Push".

**E. Proposing Changes for Review (Creating a Pull Request - PR):**
Ask for your branch to be merged into `main`.

- **GitHub Desktop:** After pushing, it often prompts to "Create Pull Request".
- **VS Code / GitHub Website:**
  1.  Go to the repository on GitHub.
  2.  You'll often see a prompt for your recently pushed branch. Click "Compare & pull request".
  3.  Alternatively, go to the "Pull requests" tab and click "New pull request".
  4.  Select your branch to compare against `main`. Add a title/description and create the PR.

**Key Team Practices:**

- **Communicate** with your team.
- **Pull frequently** to stay updated.
- **Commit often** with clear messages.
- **Use branches** for all new work.
- **Review Pull Requests** if your team uses them.

---

## Database Setup

- Start XAMPP (Apache & MySQL).
- Go to `http://localhost/phpmyadmin`.
- Create a new database (e.g., `echoes_today_db`).
- Import any provided `.sql` schema file, or coordinate with the team for table structures.
- Configure `config/config.php` with your database details (usually `root` username, empty password for default XAMPP).

## Running the Application

- Public View: `http://localhost/Echoes-of-today-Newspaper/public/`
- Admin Panel: `http://localhost/Echoes-of-today-Newspaper/admin/`
- Journalist Panel: `http://localhost/Echoes-of-today-Newspaper/journalist/`
