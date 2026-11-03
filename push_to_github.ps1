Set-Location -Path "C:\xampp\htdocs\vapefinalwebsite"
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "Pushing Infinity Vape Codebase to GitHub" -ForegroundColor Cyan
Write-Host "Repo: https://github.com/Mahad0300/infinityvape.git" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

git remote set-url origin https://github.com/Mahad0300/infinityvape.git
git branch -M main

Write-Host "`n[1/3] Adding all files (nothing ignored)..." -ForegroundColor Yellow
git add -A

Write-Host "`n[2/3] Committing changes..." -ForegroundColor Yellow
git commit -m "Push Infinity Vape Shop full project"

Write-Host "`n[3/3] Pushing to main branch on GitHub..." -ForegroundColor Yellow
git push -u origin main

Write-Host "`nDone!" -ForegroundColor Green

