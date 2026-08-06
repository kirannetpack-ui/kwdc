@echo off
echo Getting login page...
curl -c cookies.txt -s http://127.0.0.1:8000/login > login.html

echo Extracting CSRF token...
for /f "tokens=*" %%a in ('findstr "_token" login.html') do set line=%%a

echo Token line: %line%
echo.
echo Now copy the token value and run:
echo curl -X POST http://127.0.0.1:8000/login -b cookies.txt -c cookies.txt -H "Content-Type: application/x-www-form-urlencoded" -d "_token=TOKEN_HERE" -d "email=admin@ktmwdc.com" -d "password=password123"