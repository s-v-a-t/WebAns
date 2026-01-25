var cmInstances = {};
var currentView = 'main';
function initCM(id, mode) {
    var el = document.getElementById(id);
    if (el && !el.nextElementSibling?.classList.contains('CodeMirror')) {
        cmInstances[id] = CodeMirror.fromTextArea(el, {mode: mode, lineNumbers: true});
        cmInstances[id].getDoc().markClean();
    }
}
function updateGroupCount(el){
    let g=el.closest('details'),cb=g.querySelectorAll('input[name="hosts[]"]'),c=Array.from(cb).filter(x=>x.checked).length,sa=g.querySelector('.select-all-label input');
    g.querySelector('.host-count').textContent=`(${c}/${cb.length})`; sa.checked=c===cb.length; sa.indeterminate=c>0&&c<cb.length;
}
function toggleGroup(s){ s.closest('details').querySelectorAll('input[name="hosts[]"]').forEach(c=>c.checked=s.checked); updateGroupCount(s); }
function refreshAppData() {
    var activeTab = 'General';
    if (currentView === 'settings') {
        var tabs = document.querySelectorAll('.tabcontent');
        for(var i=0; i<tabs.length; i++) {
            if(tabs[i].style.display === 'block') {
                activeTab = tabs[i].id;
                break;
            }
        }
    }
    fetch('?act=refresh_state').then(r=>r.json()).then(d => {
        if (d.main) {
            var mv = document.getElementById('main-view');
            if (mv) {
                mv.outerHTML = d.main;
                mv = document.getElementById('main-view');
                if (mv) mv.style.display = (currentView === 'main' ? 'block' : 'none');
            }
            initPlaybookSelector();
        }
        if (d.settings) {
            var sv = document.getElementById('settings-view');
            if (sv) {
                sv.outerHTML = d.settings;
                sv = document.getElementById('settings-view');
                if (sv) sv.style.display = (currentView === 'settings' ? 'block' : 'none');
            }
            if (currentView === 'settings') {
                cmInstances = {}; 
                var btn = document.querySelector(`button[onclick*="'${activeTab}'"]`);
                if (btn) btn.click();
                else { var dt = document.getElementById('defaultOpen'); if (dt) dt.click(); }
            }
        }
        if (d.groups) groupsData = d.groups;
        if (d.hosts) hostsData = d.hosts;
    }).catch(e => console.error('Refresh error:', e));
}

function switchView(view, skipRefresh) {
    if (view !== 'reports') curRep = null;
    fetch('?check_auth=1').then(r=>r.text()).then(t=>{if(t!=='OK')window.location.reload();});
    var runErrorAlert = document.getElementById('run-error-alert');
    if (runErrorAlert) runErrorAlert.style.display = 'none';
    if (!skipRefresh && (view === 'main' || view === 'settings')) refreshAppData();
    ['main', 'settings', 'reports'].forEach(v => {
        const el = document.getElementById(v + '-view');
        if (el) el.style.display = (view === v ? 'block' : 'none');
    });
    currentView = view;
    if (view === 'settings') {
        if (typeof cancelEditGroup === 'function') cancelEditGroup();
        if (typeof cancelEditHost === 'function') cancelEditHost();
        for (const id in cmInstances) {
            if (cmInstances.hasOwnProperty(id) && cmInstances[id]) {
                cmInstances[id].toTextArea();
            }
        }
        cmInstances = {};
        var defaultTab = document.getElementById('defaultOpen');
        if (defaultTab) defaultTab.click();
    }
}
var curRep = null;
var curRepOffset = 0;
function loadReport(id) {
    if(curRep !== id) {
        document.getElementById('report-content').innerHTML = '<div class="panel">' + t('loading') + '</div>';
        curRepOffset = 0;
    }
    curRep = id;
    document.querySelectorAll('.report-item').forEach(el => el.classList.remove('active'));
    var item = document.getElementById('rep-' + id); if(item) { item.classList.add('active'); item.classList.remove('unread'); }
    fetch('?act=get_report&id=' + id + '&offset=' + curRepOffset).then(r => r.json()).then(d => {
        if(curRep !== id) return;
        if (curRepOffset === 0) {
            document.getElementById('report-content').innerHTML = (d.header||'') + '<div id="rep-parsed">' + (d.parsed||'') + '</div><details class="raw-log-details"><summary>' + t('show_raw_log') + '</summary><div class="result-box"><pre id="rep-raw"></pre></div></details>';
        } else {
            document.getElementById('rep-parsed').innerHTML = d.parsed;
        }
        if (d.delta) document.getElementById('rep-raw').textContent += d.delta;
        curRepOffset = d.length;
        if(item && d.status) { var s = item.querySelector('div[class^="status-"]'); if(s) { s.className = 'status-' + d.status + ' report-status-badge'; s.innerText = d.status; } }
        if(d.status === 'running') setTimeout(() => { if(curRep === id) loadReport(id); }, 2000);
    }).catch(e => {
        console.error('Fetch error:', e);
        document.getElementById('report-content').innerHTML = '<div class="panel error-text">' + t('error_loading') + e + '</div>';
    });
}
function getCookie(n){let m=document.cookie.match(new RegExp('(^| )'+n+'=([^;]+)'));return m?m[2]:null;}
var openRep = getCookie('open_report');
if(openRep){ switchView('reports'); loadReport(openRep); document.cookie = "open_report=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; }
document.querySelectorAll('details.host-group').forEach(g=>{let c=g.querySelector('input[name="hosts[]"]');if(c)updateGroupCount(c)});
function openTab(e,n){
    var i,tc=document.getElementsByClassName("tabcontent"),tl=document.getElementsByClassName("tablinks");
    for(i=0;i<tc.length;i++)tc[i].style.display="none";
    for(i=0;i<tl.length;i++)tl[i].className=tl[i].className.replace(" active","");
    document.getElementById(n).style.display="block";e.currentTarget.className+=" active";
    if(n==='Playbooks') {
        initCM('playbook_content', 'yaml');
        document.querySelector('#Playbooks select').value = '';
        loadPlaybook(''); 
        setTimeout(()=>cmInstances['playbook_content']?.refresh(),10);
    }
    if(n==='Config') {
        initCM('ansible_cfg', 'properties');
        const editor = cmInstances['ansible_cfg'];
        if (editor) {
            editor.setValue(document.getElementById('ansible_cfg').defaultValue);
            editor.getDoc().markClean();
            setTimeout(() => editor.refresh(), 10);
        }
    }
    if(n==='Groups') { cancelEditGroup(); initCM('group_vars', 'properties'); }
    if(n==='Hosts') { cancelEditHost(); }
    
    if(e.isTrusted){
        var a=document.getElementsByClassName("alert");for(i=0;i<a.length;i++)a[i].style.display="none";
        if(window.history.replaceState){var u=new URL(window.location);u.searchParams.delete('msg');u.searchParams.delete('type');window.history.replaceState({},'',u);}
    }
}
function editGroup(name) {
    var g = groupsData.find(x => x.name === name); if (!g) return;
    document.getElementById('groups-list').style.display = 'none'; document.getElementById('groups-edit').style.display = 'block';
    document.querySelector('input[name="update_group_name"]').value = g.name; document.getElementById('edit_group_title').innerText = g.name;
    var cm = cmInstances['group_vars'];
    if (cm) { cm.setValue(g.vars); cm.getDoc().markClean(); setTimeout(() => cm.refresh(), 10); } else { document.getElementById('group_vars').value = g.vars; initCM('group_vars', 'properties'); }
}
function cancelEditGroup() { 
    document.getElementById('groups-list').style.display = 'block'; document.getElementById('groups-edit').style.display = 'none'; 
    if (cmInstances['group_vars']) { cmInstances['group_vars'].setValue(''); cmInstances['group_vars'].getDoc().markClean(); }
}
function editHost(hostname, group) {
    var h = hostsData.find(x => x.hostname === hostname && x.group_name === group); if (!h) return;
    document.getElementById('hosts-list').style.display = 'none'; document.getElementById('hosts-edit').style.display = 'block';
    document.querySelector('input[name="update_host_original_name"]').value = h.hostname; document.querySelector('input[name="update_host_group"]').value = h.group_name;
    document.querySelector('#hosts-edit input[name="hostname"]').value = h.hostname; document.querySelector('#hosts-edit input[name="ip_address"]').value = h.ip_address;
    document.querySelector('#hosts-edit input[name="params"]').value = h.params; document.querySelector('#hosts-edit select[name="new_group_name"]').value = h.group_name;
    document.getElementById('edit_host_title').innerText = h.hostname;
}
function cancelEditHost() { document.getElementById('hosts-list').style.display = 'block'; document.getElementById('hosts-edit').style.display = 'none'; }
function loadPlaybook(file) {
    var nameInput = document.querySelector('input[name="playbook_filename"]');
    var deleteBtn = document.getElementById('btn-delete-playbook');
    var displayName = file.replace(/\.yml$/, '');
    nameInput.value = displayName;
    if (!file) { 
        nameInput.readOnly = false;
        if(deleteBtn) deleteBtn.style.display = 'none';
        if(cmInstances['playbook_content']) { cmInstances['playbook_content'].setValue(''); cmInstances['playbook_content'].getDoc().markClean(); } 
        return; 
    }
    nameInput.readOnly = true;
    if(deleteBtn) deleteBtn.style.display = 'inline-block';
    fetch('?act=get_playbook&file=' + encodeURIComponent(file)).then(r => r.text()).then(t => { if(cmInstances['playbook_content']) { cmInstances['playbook_content'].setValue(t); cmInstances['playbook_content'].getDoc().markClean(); } });
}
function saveForm(e, form) {
    if (form.dataset.confirm && !confirm(form.dataset.confirm)) {
        e.preventDefault(); return;
    }
    e.preventDefault(); 
    var savedCmIds = [];
    form.querySelectorAll('textarea').forEach(ta => { if (cmInstances[ta.id]) { cmInstances[ta.id].save(); savedCmIds.push(ta.id); } });
    const fd = new FormData(form); fd.append('ajax', '1');
    fetch(window.location.href, { method: 'POST', body: fd }).then(r => r.json()).then(d => { 
        if (!d.success && d.error_type === 'syntax') {
            showErrorModal(d.message);
        } else {
            showToast(d.message, d.success ? 'success' : 'error');
        }
        if (d.success) { 
            if (d.force_reload) { 
                sessionStorage.setItem('webans_view', currentView);
                if (currentView === 'settings') {
                    var tabs = document.querySelectorAll('.tabcontent');
                    for(var i=0; i<tabs.length; i++) {
                        if(tabs[i].style.display === 'block') { sessionStorage.setItem('webans_tab', tabs[i].id); break; }
                    }
                }
                window.location.reload(); return; 
            }
            savedCmIds.forEach(id => { if (cmInstances[id]) { cmInstances[id].getDoc().markClean(); const ta = cmInstances[id].getTextArea(); if (ta) { ta.defaultValue = ta.value; } } }); 
            if (form.dataset.reload) { refreshAppData(); }
        } 
    }).catch(e => showToast(t('error_save'), 'error'));
}
var toastTimeout;
function showToast(msg, type) { let t = document.getElementById('toast'); t.textContent = msg; t.className = 'toast show ' + type; clearTimeout(toastTimeout); toastTimeout = setTimeout(() => t.className = t.className.replace('show', ''), 6000); }
var defaultTab = document.getElementById("defaultOpen");
if (defaultTab) defaultTab.click();
function deleteCurrentPlaybook() {
    var select = document.querySelector('.playbook-load-select');
    var file = select.value;
    if (!file) return;
    if (!confirm(t('delete_playbook_confirm') + file + '?')) return;
    var fd = new FormData();
    fd.append('csrf', document.querySelector('input[name="csrf"]').value);
    fd.append('delete_playbook_name', file);
    fd.append('ajax', '1');
    fetch(window.location.href, { method: 'POST', body: fd }).then(r => r.json()).then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
        if (d.success) { refreshAppData(); }
    }).catch(e => showToast(t('error_delete'), 'error'));
}
function checkPlaybookAndToggleHosts() {
    const playbookSelector = document.querySelector('select[name="playbook"]'); if (!playbookSelector) return;
    const hostsContainer = document.querySelector('.hosts-container'); const selectedFile = playbookSelector.value;
    if (!selectedFile) { hostsContainer.classList.remove('disabled'); hostsContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false); return; }
    fetch('?act=get_playbook&file=' + encodeURIComponent(selectedFile)).then(response => response.text()).then(content => {
        const match = content.match(/(?:^\s*-\s*hosts|^\s*hosts):\s*(.*)/m); let isStaticHost = true;
        if (match) { 
            let target = match[1];
            const commentIndex = target.indexOf('#');
            if (commentIndex !== -1) target = target.substring(0, commentIndex);
            target = target.trim();
            if ((target.startsWith('"') && target.endsWith('"')) || (target.startsWith("'") && target.endsWith("'"))) { target = target.substring(1, target.length - 1); } if (target.startsWith('{{') && target.endsWith('}}')) { isStaticHost = false; } 
        }
        if (isStaticHost) { hostsContainer.classList.add('disabled'); hostsContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.disabled = true; cb.checked = false; }); document.querySelectorAll('details.host-group').forEach(g => { let anyCheckbox = g.querySelector('input[type="checkbox"]'); if (anyCheckbox) { updateGroupCount(anyCheckbox); } }); } else { hostsContainer.classList.remove('disabled'); hostsContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false); }
    }).catch(error => { console.error('Error fetching playbook content:', error); hostsContainer.classList.remove('disabled'); hostsContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false); });
}
function initPlaybookSelector() {
    const playbookSelector = document.querySelector('select[name="playbook"]'); if (playbookSelector) { playbookSelector.addEventListener('change', checkPlaybookAndToggleHosts); checkPlaybookAndToggleHosts(); }
}
initPlaybookSelector();
function filterHosts(){ var f=document.getElementById("hostGroupFilter").value,r=document.querySelectorAll("#Hosts table tbody tr"); for(var i=0;i<r.length;i++)r[i].style.display=(f===""||r[i].getAttribute('data-group')===f)?"":"none"; }
function showErrorModal(msg) {
    let m = document.getElementById('error-modal');
    if (!m) {
        m = document.createElement('div');
        m.id = 'error-modal';
        m.className = 'modal-overlay';
        m.innerHTML = '<div class="modal-content">' +
            '<h3 class="modal-title">' + t('syntax_error') + '</h3>' +
            '<pre id="error-modal-text" class="modal-pre"></pre>' +
            '<button onclick="document.getElementById(\'error-modal\').style.display=\'none\'" class="modal-close-btn">' + t('close') + '</button>' +
            '</div>';
        document.body.appendChild(m);
    }
    document.getElementById('error-modal-text').textContent = msg;
    m.style.display = 'flex';
}
var savedView = sessionStorage.getItem('webans_view');
if (savedView && document.getElementById('main-view')) {
    switchView(savedView, true);
    sessionStorage.removeItem('webans_view');
    if (savedView === 'settings') {
        var savedTab = sessionStorage.getItem('webans_tab');
        if (savedTab) { var btn = document.querySelector(`button[onclick*="'${savedTab}'"]`); if (btn) btn.click(); sessionStorage.removeItem('webans_tab'); }
    }
} else if (savedView) {
    sessionStorage.removeItem('webans_view');
    sessionStorage.removeItem('webans_tab');
}