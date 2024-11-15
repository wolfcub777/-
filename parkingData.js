const parkingData = {
    first: Array(454).fill().map((_, i) => ({
        id: i + 1,
        status: 'available',
        description: '',
        lastModified: new Date().toISOString()
    })),
    second: Array(454).fill().map((_, i) => ({
        id: i + 1,
        status: 'occupied',
        description: '已占用',
        lastModified: new Date().toISOString()
    })),
    third: Array(454).fill().map((_, i) => ({
        id: i + 1,
        status: 'not-available',
        description: '維修中',
        lastModified: new Date().toISOString()
    }))
};

// 更新指定停車位的描述和狀態
function updateParkingSpace(index, period, description, status) {
    if (index >= 0 && index < 454) {  // 檢查索引是否在有效範圍內
        const space = parkingData[period][index];
        space.description = description;
        space.status = status;
        space.lastModified = new Date().toISOString();
    } else {
        console.error('索引超出範圍。');
    }
}

// 測試範例：更新第一時段第100個停車位
updateParkingSpace(70, 'first', '已預訂', 'occupied'); // 更新第71個停車位
console.log(parkingData.first[70]); // 查看更新後的第71個停車位資訊
