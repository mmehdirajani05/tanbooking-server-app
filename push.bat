@echo off
echo ========================================
echo GitHub Push - Authentication Required
echo ========================================
echo.
echo IMPORTANT: GitHub requires a Personal Access Token (NOT your password)
echo.
echo To get a token:
echo 1. Go to: https://github.com/settings/tokens/new
echo 2. Give it a name (e.g., "TanBooking PC")
echo 3. Check the "repo" scope
echo 4. Click "Generate token"
echo 5. COPY THE TOKEN (you won't see it again!)
echo.
echo Then enter your credentials below:
echo.

cd C:\Hadi\Projects\tanbooking-server-app

echo When prompted for Username, enter: mmehdirajani05
echo When prompted for Password, PASTE your Personal Access Token
echo.
pause

git push origin main

pause
