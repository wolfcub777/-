const parkingData = {
    first: Array(70).fill().map((_, i) => ({
        id: i + 1,
        status: 'available',
        description: '',
        lastModified: new Date().toISOString()
    })),
    second: Array(70).fill().map((_, i) => ({
        id: i + 1,
        status: 'occupied',
        description: '已占用',
        lastModified: new Date().toISOString()
    })),
    third: Array(70).fill().map((_, i) => ({
        id: i + 1,
        status: 'not-available',
        description: '維修中',
        lastModified: new Date().toISOString()
    }))
};

// 更新指定停車位的描述和狀態
function updateParkingSpace(index, period, description, status) {
    if (index >= 0 && index < 70) {  // 檢查索引是否在有效範圍內
        const space = parkingData[period][index];
        space.description = description;
        space.status = status;
        space.lastModified = new Date().toISOString();

        // 將更新後的資料儲存到 localStorage
        localStorage.setItem('parkingData2', JSON.stringify(parkingData));
    } else {
        console.error('索引超出範圍。');
    }
}

// 測試範例：更新第一時段第70個停車位
updateParkingSpace(69, 'first', '已預訂', 'occupied');
console.log(parkingData.first[69]); // 查看更新後的第70個停車位資訊

// 在網頁載入或關閉時儲存資料到 localStorage
window.addEventListener('beforeunload', function () {
    localStorage.setItem('parkingData2', JSON.stringify(parkingData));
});
