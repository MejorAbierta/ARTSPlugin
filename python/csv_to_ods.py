import subprocess

def csv_to_ods(csv_file_path, ods_file_path):
    try:
        
        if check_unoconv():
            print("Using unoconv")
            subprocess.run(['unoconv', '-f', 'ods', csv_file_path, '-o', ods_file_path])

        elif check_soffice():
            print("Using soffice (LibreOffice)")
            subprocess.run(['soffice', '--headless', '--convert-to', 'ods', csv_file_path, '--outdir', ''])
        else:
           print("Ensure you have unoconv or soffice (part of LibreOffice) installed on your system.") 

        # Manually move the converted file
        import os
        import shutil
        for filename in os.listdir('.'):
            if filename.endswith('.ods') and filename != os.path.basename(ods_file_path):
                shutil.move(filename, ods_file_path)
                break

        print(f"Successfully converted {csv_file_path} to {ods_file_path}")

    except Exception as e:
        print(f"An error occurred: {e}") 


def check_unoconv():
    try:
        subprocess.run(['unoconv', '--version'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        return True
    except FileNotFoundError:
        return False

def check_soffice():
    try:
        subprocess.run(['soffice', '--version'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        return True
    except FileNotFoundError:
        return False

csv_file_path = 'output.csv' 
ods_file_path = 'output.ods'

csv_to_ods(csv_file_path, ods_file_path)