import sys
from pathlib import Path
try:
    from PyPDF2 import PdfReader
except Exception as e:
    print('PyPDF2 import error', e)
    sys.exit(1)
pdf_path = Path(r"C:/Users/RAHI/OneDrive/Documents/HTML/studenthub_user/Practical_List_ITUE203_WDF_2026_27.pdf")
if not pdf_path.is_file():
    print('PDF not found')
    sys.exit(1)
reader = PdfReader(str(pdf_path))
text = ''
for page in reader.pages:
    txt = page.extract_text()
    if txt:
        text += txt + '\n'
print(text)
