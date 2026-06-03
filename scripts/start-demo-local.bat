@echo off
setlocal

cd /d "%~dp0\.."

echo Limpando caches do Laravel...
php artisan optimize:clear
if errorlevel 1 goto erro

echo Aplicando migrations pendentes...
php artisan migrate --force
if errorlevel 1 goto erro

echo Garantindo usuarios de demonstracao...
php artisan db:seed --force
if errorlevel 1 goto erro

echo.
echo UniSalas disponivel em:
echo http://10.0.0.77:8000
echo.
echo Mantenha esta janela aberta durante a apresentacao.
echo Para encerrar, pressione Ctrl+C.
echo.

php artisan serve --host=0.0.0.0 --port=8000
goto fim

:erro
echo.
echo Ocorreu um erro ao preparar a demonstracao.
echo Verifique se o MySQL do XAMPP esta ligado e se a porta 8000 esta livre.
pause

:fim
endlocal
