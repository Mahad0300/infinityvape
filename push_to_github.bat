@echo off
cd /d "C:\xampp\htdocs\vapefinalwebsite"
echo ===================================================
echo Pushing Infinity Vape Codebase to GitHub
echo Repo: https://github.com/Mahad0300/infinityvape.git
echo ===================================================
echo.

git remote set-url origin https://github.com/Mahad0300/infinityvape.git
git branch -M main

echo [1/3] Adding all files (including vendor, storage, .env, sql)...
git add -A

echo [2/3] Committing changes...
git commit -m "Push Infinity Vape Shop full project"

echo [3/3] Pushing to main branch on GitHub...
git push -u origin main

echo.
echo ===================================================
if %ERRORLEVEL% EQU 0 (
    echo SUCCESS: Project pushed to GitHub successfully!
) else (
    echo PUSH FAILED: Please check GitHub credentials or permissions above.
)
echo ===================================================
echo.
pause

