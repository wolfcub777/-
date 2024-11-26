let parkingData = JSON.parse(localStorage.getItem('parkingData')) || {
    first: Array(454).fill().map((_, i) => ({ id: i + 1, status: 'available', description: '', lastModified: '' })),
    second: Array(454).fill().map((_, i) => ({ id: i + 1, status: 'available', description: '', lastModified: '' })),
    third: Array(454).fill().map((_, i) => ({ id: i + 1, status: 'available', description: '', lastModified: '' }))
};

// 更新指定停車位的描述和狀態
function updateParkingSpace(id, period, description, status) {
    if (!parkingData[period]) {
        console.error(`無效的時ㄌ段名稱: ${period}`);
        return;
    }

    if (id >= 1 && id <= 454) {
        const spaceIndex = id - 1;
        const space = parkingData[period][spaceIndex];
        if (space) {
            space.description = description;
            space.status = status;
            space.lastModified = new Date().toLocaleString();
            console.log(`更新成功: 停車位 ${id} (${period})`);
        } else {
            console.error(`未找到對應的停車位資料，ID: ${id}, 時段: ${period}`);
        }
    } else {
        console.error('ID 超出有效範圍 (1-454)');
    }
}

// 測試範例：更新第一時段第454個停車位 
updateParkingSpace(454, 'first', '已預訂', 'occupied');
console.log(parkingData.first[453]); // 查看更新後的第454個停車位資訊
