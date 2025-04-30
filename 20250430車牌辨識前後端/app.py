from flask import Flask, jsonify, render_template
from flask_cors import CORS
import pytesseract
from PIL import Image
import numpy as np
import cv2
import os
import re

app = Flask(__name__, static_folder='.', template_folder='.')
CORS(app)

IMAGE_PATH = r"C:\Users\benso\myapp\98.jpg"
TEXT_OUTPUT_PATH = r"C:\Users\benso\myapp\ocr_output.txt"  # 儲存文字的路徑

pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

# 用來儲存最後辨識結果（避免前端重複處理）
last_result = ""

@app.route('/')
def home():
    return render_template("前端.html")

@app.route('/predict', methods=['GET'])
def predict():
    global last_result  # 使用全域變數
    if not os.path.exists(IMAGE_PATH):
        return jsonify({"error": "指定路徑的圖片不存在"}), 404

    try:
        image = Image.open(IMAGE_PATH)
        print("✅ 圖片已成功開啟")
        image = np.array(image)

        # 放大圖片
        image = cv2.resize(image, None, fx=3, fy=3, interpolation=cv2.INTER_LINEAR)

        # 灰階
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

        # 黑白反轉
        gray = cv2.bitwise_not(gray)

        # 直方圖均衡化 + 模糊
        gray = cv2.equalizeHist(gray)
        gray = cv2.GaussianBlur(gray, (3, 3), 0)

        # 二值化
        _, binary = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

        processed_image = Image.fromarray(binary)

        config = "--psm 7 --oem 3 -c tessedit_char_whitelist=ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-"
        text = pytesseract.image_to_string(processed_image, lang="eng", config=config).strip()

        match = re.search(r'[A-Z]{3}-\d{4}', text)
        final_text = match.group() if match else text

        last_result = final_text if final_text else "無法辨識文字"

        return jsonify({
            "image_path": IMAGE_PATH,
            "text": last_result
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/save', methods=['POST'])
def save_text():
    try:
        with open(TEXT_OUTPUT_PATH, 'w', encoding='utf-8') as f:
            f.write(last_result)
        return jsonify({"message": f"✅ 辨識結果已儲存至：{TEXT_OUTPUT_PATH}"})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
