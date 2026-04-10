@echo off
echo ========================================
echo Push Feature Branch to GitHub
echo ========================================
echo.
echo Branch: feature/complete-hotel-booking-system
echo.
echo Once you have collaborator access:
echo 1. Username: mmehdirajani05
echo 2. Password: [Your GitHub Personal Access Token]
echo.
echo Get token from: https://github.com/settings/tokens
echo.
pause

cd C:\Hadi\Projects\tanbooking-server-app
git push origin feature/complete-hotel-booking-system

echo.
echo ========================================
echo After pushing successfully:
echo ========================================
echo 1. Go to: https://github.com/mmehdirajani05/tanbooking-server-app
echo 2. Click "Compare ^& pull request"
echo 3. Base: main ^<- Compare: feature/complete-hotel-booking-system
echo 4. Add description and click "Create pull request"
echo.
pause
