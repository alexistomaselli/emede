import fitz  # PyMuPDF
import os
import io
from PIL import Image

pdf_path = "Sitio.pdf"
output_dir = "extracted_assets"

if not os.path.exists(output_dir):
    os.makedirs(output_dir)

def extract_content():
    try:
        doc = fitz.open(pdf_path)
        full_text = ""
        
        print(f"Opened PDF with {len(doc)} pages.")

        for page_num, page in enumerate(doc):
            text = page.get_text()
            full_text += f"--- Page {page_num + 1} ---\n{text}\n"
            
            # Extract images
            image_list = page.get_images(full=True)
            for img_index, img in enumerate(image_list):
                xref = img[0]
                base_image = doc.extract_image(xref)
                image_bytes = base_image["image"]
                image_ext = base_image["ext"]
                image_filename = f"{output_dir}/image_p{page_num+1}_{img_index}.{image_ext}"
                
                with open(image_filename, "wb") as f:
                    f.write(image_bytes)
                print(f"Saved {image_filename}")

        with open("pdf_content.txt", "w", encoding="utf-8") as f:
            f.write(full_text)
            
        print("Text extracted to pdf_content.txt")
        
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    extract_content()
