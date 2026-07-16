function removeTreeConnectors() {
    ['org-connector-svg', 'tree-svg-connector', 'middle-connector-svg', 'pokja-connector-svg'].forEach(id => {
        document.getElementById(id)?.remove();
    });
}

function shouldDrawTreeConnectors() {
    return window.innerWidth > 768;
}

function drawTreeConnectors() {
    const treeContainer = document.getElementById('top3Tree');
    const scrollContainer = document.querySelector('.struktur-scroll');
    const ketuaPkkWrapper = document.getElementById('wrapper-ketua-pkk');
    const rowSekben = document.getElementById('row-sekben');
    const rowPokja = document.getElementById('row-pokja');
    
    if (!treeContainer || !scrollContainer || !ketuaPkkWrapper || !rowSekben || !rowPokja) return;
    if (!shouldDrawTreeConnectors()) { removeTreeConnectors(); return; }
    
    removeTreeConnectors();
    const containerRect = scrollContainer.getBoundingClientRect();
    const topCards = Array.from(treeContainer.querySelectorAll('.org-card-wrapper .org-card'));
    const sekbenCards = Array.from(rowSekben.querySelectorAll('.org-card-wrapper .org-card'));
    const pokjaCards = Array.from(rowPokja.querySelectorAll('.org-card-wrapper .org-card'));
    if (topCards.length < 3 || sekbenCards.length < 2 || pokjaCards.length < 2) return;
    
    const toLocalRect = (el) => {
        const r = el.getBoundingClientRect();
        return {
            left: r.left - containerRect.left, right: r.right - containerRect.left,
            top: r.top - containerRect.top, bottom: r.bottom - containerRect.top,
            centerX: (r.left + r.width / 2) - containerRect.left,
            centerY: (r.top + r.height / 2) - containerRect.top
        };
    };
    
    const topRects = topCards.map(toLocalRect).sort((a, b) => a.centerX - b.centerX);
    const sekRects = sekbenCards.map(toLocalRect);
    const pokjaRects = pokjaCards.map(toLocalRect);
    const ketuaRect = toLocalRect(ketuaPkkWrapper.querySelector('.org-card'));
    const leftTopRect = topRects[0], centerTopRect = topRects[Math.floor(topRects.length / 2)], rightTopRect = topRects[topRects.length - 1];
    const topSideLineY = centerTopRect.centerY;
    const ketuaBottomY = ketuaRect.bottom;
    const sekLeftX = sekRects[0].centerX, sekRightX = sekRects[1].centerX;
    const sekTargetY = Math.min(...sekRects.map(r => r.top)) - 6;
    const sekBottomY = Math.max(...sekRects.map(r => r.bottom));
    const sekBranchY = ketuaBottomY + (sekTargetY - ketuaBottomY) * 0.45;
    const pokjaLeftX = pokjaRects[0].centerX, pokjaRightX = pokjaRects[pokjaRects.length - 1].centerX;
    const pokjaTargetY = Math.min(...pokjaRects.map(r => r.top)) - 6;
    const pokjaBranchY = Math.max(sekBottomY + 16, sekTargetY + (pokjaTargetY - sekTargetY) * 0.62);
    
    const svgHeight = pokjaTargetY + 120;
    const svgNS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(svgNS, 'svg');
    svg.setAttribute('id', 'org-connector-svg');
    svg.setAttribute('style', `position:absolute;top:0;left:0;width:${scrollContainer.scrollWidth}px;height:${svgHeight}px;pointer-events:none;z-index:1;`);
    
    const addLine = (x1, y1, x2, y2) => {
        const line = document.createElementNS(svgNS, 'line');
        line.setAttribute('x1', x1); line.setAttribute('y1', y1); line.setAttribute('x2', x2); line.setAttribute('y2', y2);
        line.setAttribute('stroke', '#2c5282'); line.setAttribute('stroke-width', '2');
        svg.appendChild(line);
    };
    
    const leftStartX = leftTopRect.right + 8, leftEndX = centerTopRect.left - 8;
    if (leftStartX < leftEndX) addLine(leftStartX, topSideLineY, leftEndX, topSideLineY);
    const rightStartX = centerTopRect.right + 8, rightEndX = rightTopRect.left - 8;
    if (rightStartX < rightEndX) addLine(rightStartX, topSideLineY, rightEndX, topSideLineY);
    
    addLine(ketuaRect.centerX, ketuaBottomY, ketuaRect.centerX, sekBranchY);
    addLine(sekLeftX, sekBranchY, sekRightX, sekBranchY);
    addLine(sekLeftX, sekBranchY, sekLeftX, sekTargetY);
    addLine(sekRightX, sekBranchY, sekRightX, sekTargetY);
    addLine(ketuaRect.centerX, sekBranchY, ketuaRect.centerX, pokjaBranchY);
    addLine(pokjaLeftX, pokjaBranchY, pokjaRightX, pokjaBranchY);
    pokjaRects.forEach(rect => addLine(rect.centerX, pokjaBranchY, rect.centerX, pokjaTargetY));
    
    scrollContainer.style.position = 'relative';
    scrollContainer.appendChild(svg);
}

function togglePokja(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    content.classList.toggle('collapsed');
    icon.classList.toggle('collapsed');
}

// Init & Resize Listeners
window.addEventListener('load', () => setTimeout(drawTreeConnectors, 300));
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => drawTreeConnectors(), 200);
});