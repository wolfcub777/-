from flask import Flask, jsonify, render_template, request
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
TEXT_OUTPUT_PATH = r"C:\Users\benso\myapp\ocr_output.txt"
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

last_result = ""  # 全域變數儲存辨識文字


# 🔁 共用的 OCR 處理邏輯
def ocr_process(image_input):
    image = np.array(image_input)
    image = cv2.resize(image, None, fx=3, fy=3, interpolation=cv2.INTER_LINEAR)
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    gray = cv2.bitwise_not(gray)
    gray = cv2.equalizeHist(gray)
    gray = cv2.GaussianBlur(gray, (3, 3), 0)
    _, binary = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
    processed_image = Image.fromarray(binary)

    config = "--psm 7 --oem 3 -c tessedit_char_whitelist=ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-"
    text = pytesseract.image_to_string(processed_image, lang="eng", config=config).strip()
    match = re.search(r'[A-Z]{3}-\d{4}', text)
    return match.group() if match else (text if text else "無法辨識文字")


@app.route('/')
def home():
    return render_template("前端.html")


@app.route('/predict', methods=['GET'])
def predict():
    global last_result
    if not os.path.exists(IMAGE_PATH):
        return jsonify({"error": "指定路徑的圖片不存在"}), 404

    try:
        image = Image.open(IMAGE_PATH)
        print("✅ 使用固定圖片進行辨識")
        last_result = ocr_process(image)
        return jsonify({
            "image_path": IMAGE_PATH,
            "text": last_result
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500


@app.route('/upload', methods=['POST'])
def upload():
    global last_result
    if 'image' not in request.files:
        return jsonify({"error": "未提供圖片檔案"}), 400

    file = request.files['image']
    if file.filename == '':
        return jsonify({"error": "檔名為空"}), 400

    try:
        image = Image.open(file.stream)
        print("✅ 接收到使用者上傳的圖片")
        last_result = ocr_process(image)
        return jsonify({"text": last_result})
    except Exception as e:
        return jsonify({"error": str(e)}), 500


@app.route('/save', methods=['POST'])
def save_text():
    try:
        with open(TEXT_OUTPUT_PATH, 'a', encoding='utf-8') as f:
            f.write(last_result + '\n')  # ✅ 注意這行要縮排
        return jsonify({"message": f"✅ 已附加儲存至：{TEXT_OUTPUT_PATH}"})
    except Exception as e:
        return jsonify({"error": str(e)}), 500


if __name__ == '__main__':
    app.run(debug=True)
