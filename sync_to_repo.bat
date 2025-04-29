@echo off
xcopy /E /H /Y /I "C:\xampp\htdocs\LicitHub_PHP" "C:\Users\kauan_a_vieira\Documents\GitHub\PROJECT-SA-2025\LicitHub_PHP"
cd "C:\Users\kauan_a_vieira\Documents\GitHub\PROJECT-SA-2025"
git add .
git commit -m "Atualizando conteúdo do LicitHub_PHP"
git push origin main
pause
