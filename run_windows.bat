@echo off
title Khoi dong ZenTask (Docker Mode)
echo ----------------------------------------------------
echo    DANG KHOI DONG ZENTASK (DOCKER MODE)
echo ----------------------------------------------------

:: Kiem tra Docker co dang chay khong
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo LOI: Docker Desktop chua duoc bat!
    echo Vui long mo Docker Desktop va thu lai.
    pause
    exit /b
)

:: Khoi dong container
docker-compose up -d

echo Dang doi co so du lieu san sang (10 giay)...
timeout /t 10 /nobreak >nul

:: Mo trinh duyet
echo Dang mo ung dung tai http://localhost:8080
start http://localhost:8080

echo ----------------------------------------------------
echo Ung dung dang chay ngam. Ban co the dong cua so nay.
echo De tat ung dung, hay chay lenh: docker-compose down
echo ----------------------------------------------------
pause
