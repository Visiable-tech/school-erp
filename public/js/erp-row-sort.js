/* Reusable native drag/drop table sorting. Requires tbody[data-url] and tr[data-id]. */
window.ERPRowSort = {
  init(tbody, opts = {}) {
    if (!tbody || tbody.dataset.enabled !== '1') return;
    const feedback = opts.feedback;
    let dragged = null;
    let busy = false;
    const message = (txt, error = false) => {
      if (feedback) { feedback.textContent = txt; feedback.className = 'small ' + (error ? 'text-danger' : 'text-success'); }
    };
    const rows = () => [...tbody.querySelectorAll('tr[data-id]')];
    rows().forEach(row => {
      row.draggable = true;
      row.addEventListener('dragstart', e => {
        if (busy || !e.target.closest('tr')?.querySelector('.sort-handle')) { e.preventDefault(); return; }
        dragged = row;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', row.dataset.id);
        row.style.opacity = '.5';
      });
      row.addEventListener('dragover', e => { if (dragged && dragged !== row) e.preventDefault(); });
      row.addEventListener('drop', e => {
        e.preventDefault();
        if (!dragged || dragged === row || busy) return;
        const before = [...tbody.children].indexOf(dragged) > [...tbody.children].indexOf(row);
        tbody.insertBefore(dragged, before ? row : row.nextSibling);
        save();
      });
      row.addEventListener('dragend', () => { row.style.opacity = ''; dragged = null; });
    });
    async function save() {
      busy = true;
      message('Saving...');
      try {
        const response = await fetch(tbody.dataset.url, {
          method: 'POST',
          headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':opts.token },
          credentials: 'same-origin',
          body: JSON.stringify({ ids: rows().map(r => Number(r.dataset.id)) })
        });
        if (!response.ok) throw new Error('Could not save. Please refresh and try again.');
        rows().forEach((r,i) => { r.children[1].textContent = i + 1; r.children[6].textContent = i + 1; });
        message('Order saved');
      } catch (error) { message(error.message, true); }
      finally { busy = false; }
    }
  }
};
