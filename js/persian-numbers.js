(() => {
    const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
    const digitPattern = /[0-9]/g;
    const ignoredTags = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'INPUT', 'TEXTAREA', 'SELECT', 'OPTION']);

    const convertText = (root) => {
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        const textNodes = [];
        let node;
        while ((node = walker.nextNode())) textNodes.push(node);
        textNodes.forEach(textNode => {
            if (ignoredTags.has(textNode.parentElement?.tagName)) return;
            const converted = textNode.nodeValue.replace(digitPattern, digit => persianDigits[digit]);
            if (converted !== textNode.nodeValue) textNode.nodeValue = converted;
        });
    };

    convertText(document.body);
    new MutationObserver(records => records.forEach(record => {
        if (record.type === 'characterData') convertText(record.target.parentElement);
        record.addedNodes.forEach(node => {
            if (node.nodeType === Node.ELEMENT_NODE || node.nodeType === Node.TEXT_NODE) convertText(node.nodeType === Node.TEXT_NODE ? node.parentElement : node);
        });
    })).observe(document.body, { childList: true, subtree: true, characterData: true });
})();
