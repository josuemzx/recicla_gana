@echo off
setlocal enabledelayedexpansion

REM Nombre del fichero de salida
set "OUTFILE=project_contents.txt"

REM Si existe, lo borramos para partir limpio
if exist "%OUTFILE%" del /q "%OUTFILE%"

REM Cabecera
(
  echo ================================
  echo  Listado y contenido del proyecto
  echo  Generado el %date% a las %time%
  echo ================================
  echo.
) >> "%OUTFILE%"

REM Recorre carpetas y ficheros
for /R "%~dp0" %%F in (*) do (
  REM Evita incluir el propio outfile si está dentro del árbol
  if /I not "%%~nxF"=="%OUTFILE%" (
    REM Imprime separador y ruta
    (
      echo ------------------------------------------------
      echo File: %%~dpF%%~nxF
      echo ------------------------------------------------
    ) >> "%OUTFILE%"

    REM Imprime contenido
    type "%%F" >> "%OUTFILE%"

    REM Línea en blanco para separar
    echo. >> "%OUTFILE%"
  )
)

echo Listado completo en "%OUTFILE%".
pause
