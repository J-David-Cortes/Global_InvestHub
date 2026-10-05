@echo off
setlocal
rem Apagado ordenado de MariaDB. Nunca mata procesos.
set "MYSQLADMIN=C:\xampp\mysql\bin\mysqladmin.exe"
set "LOG=C:\xampp\mysql\data\mysql_error.log"

echo === Detener MariaDB (apagado ordenado con mysqladmin) ===
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
  echo AVISO: mysqld.exe no esta corriendo. No hay nada que apagar.
  goto fin
)

echo Enviando shutdown a MariaDB...
"%MYSQLADMIN%" -u root shutdown
if errorlevel 1 echo AVISO: mysqladmin devolvio error. Se seguira esperando al proceso...

set /a INTENTOS=0

:esperar
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 goto desaparecio
set /a INTENTOS+=1
if %INTENTOS% GEQ 60 goto sin_cierre
timeout /t 1 /nobreak >nul
goto esperar

:sin_cierre
echo.
echo ATENCION: mysqld.exe sigue corriendo tras 60 segundos.
echo No se ha matado el proceso. Revisalo manualmente.
goto fin

:desaparecio
echo.
echo mysqld.exe termino tras %INTENTOS% segundos.
echo Ultimas lineas del log:
powershell -NoProfile -Command "Get-Content '%LOG%' -Tail 5"
echo.
powershell -NoProfile -Command "if (Get-Content '%LOG%' -Tail 30 | Select-String 'Shutdown complete') { exit 0 } else { exit 1 }"
if errorlevel 1 (
  echo ATENCION: no se registro el cierre ^(Shutdown complete^) en el log.
) else (
  echo APAGADO LIMPIO: el log registro Shutdown complete.
)

:fin
echo.
pause
