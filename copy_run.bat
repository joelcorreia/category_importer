xcopy * "D:\sites\opencart\opencart_v1.5.3.1\" /E /Y /EXCLUDE:exclude_files.txt
pause
start http://localhost:8080/opencart/opencart_v1.5.3.1/admin/index.php