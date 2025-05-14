@echo off
setlocal enabledelayedexpansion

REM Nombre del fichero de salida
set "OUTFILE=project_contents.txt"
if exist "%OUTFILE%" del /q "%OUTFILE%"

REM Ruta completa de este script, para excluirlo
set "SCRIPT=%~f0"

REM Cabecera opcional
>>"%OUTFILE%" echo ================================
>>"%OUTFILE%" echo Listado de archivos (sin .git ni este .bat)
>>"%OUTFILE%" echo ================================

REM Recorre todos los ficheros bajo la carpeta del script
for /R "%~dp0" %%F in (*) do (
    REM Si la ruta contiene \.git\, la saltamos
    echo "%%~fF" | findstr /i "\\.git\\" >nul && (
        rem Skip .git
    ) || (
        REM Excluir este propio script
        if /I not "%%~fF"=="!SCRIPT!" (
            >>"%OUTFILE%" echo.
            >>"%OUTFILE%" echo ================================
            >>"%OUTFILE%" echo File: %%~dpnxF
            >>"%OUTFILE%" echo -------------------------------
            type "%%~fF" >>"%OUTFILE%"
        )
    )
)

endlocal
