@echo off
set ORIGINAL_DIR=%CD%
set SERVER_BIN_DIR=C:\temp\example
set PHP_DIR=C:\bin\php\php5.3.13
set SECRET_KEY=<TYPE KEY HERE>
set WEBSU_UTILITY_DIR=%ORIGINAL_DIR%
set EXTENSIONS=".txt .sql .xls"
set BINEXTENSION=.enc
set MODE=all

echo ------------------------------------------------------------
echo Encode dir: %SERVER_BIN_DIR%
echo Mode: %MODE%
echo ------------------------------------------------------------

pause

cd %SERVER_BIN_DIR%
%PHP_DIR%\php.exe %WEBSU_UTILITY_DIR%\encrypt.php mode=%MODE% extensions=%EXTENSIONS% key=%SECRET_KEY% basePath=%SERVER_BIN_DIR% binExtension=%BINEXTENSION%
cd %ORIGINAL_DIR%
pause
