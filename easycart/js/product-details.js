function switchImage(thumb, src, gallery, basePath) {
    const mainImg = document.getElementById('mainProductImg');
    if (!mainImg) return;
    
    // Add a quick fade out
    mainImg.style.opacity = '0.5';
    
    setTimeout(() => {
        const fileName = src.split('/').pop();
        mainImg.src = src;
        mainImg.style.opacity = '1';
        
        // Update active state
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        if (thumb) {
            thumb.classList.add('active');
        } else {
            // Find thumb by src if thumb not provided (for arrows)
            const thumbs = document.querySelectorAll('.thumb img');
            thumbs.forEach(tImg => {
                if (tImg.src.includes(fileName)) {
                    tImg.parentElement.classList.add('active');
                }
            });
        }
    }, 150);
}

function changeImage(direction, gallery, basePath, currentIndexObj) {
    if (!gallery || gallery.length === 0) return;
    
    currentIndexObj.index += direction;
    if (currentIndexObj.index >= gallery.length) currentIndexObj.index = 0;
    if (currentIndexObj.index < 0) currentIndexObj.index = gallery.length - 1;
    
    const nextSrc = basePath + gallery[currentIndexObj.index];
    switchImage(null, nextSrc, gallery, basePath);
}
