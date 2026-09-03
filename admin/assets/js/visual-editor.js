/**
 * FALCON CHEMICALS - LIVE VISUAL CMS CLIENT SCRIPT
 * Enables on-page inline text editing, image replacing, link editing, and instant saving.
 */
(function () {
    'use strict';

    const config = window.__FALCON_CMS__ || {
        pageName: window.location.pathname.replace(/^\//, '') || 'index.html',
        user: { name: 'Admin', role: 'admin' },
        autoStart: false
    };

    if (!config.pageName.endsWith('.html') && !config.pageName.includes('.')) {
        config.pageName = (config.pageName || 'index') + '.html';
    }

    let isEditing = false;
    let dirtyCount = 0;
    let activeImageElement = null;
    let activeLinkElement = null;

    // ── Build UI HTML ──
    function initUI() {
        // Main bottom dock
        const bar = document.createElement('div');
        bar.id = 'falcon-cms-bar';
        bar.innerHTML = `
            <div class="cms-brand">
                <div class="cms-brand-logo">F</div>
                <span>Falcon CMS</span>
            </div>
            <div class="cms-page-pill" title="Editing page: ${config.pageName}">${config.pageName}</div>
            <div class="cms-divider"></div>
            
            <div class="cms-toggle-group" id="cms-toggle-edit" title="Toggle Live Visual Editing (Ctrl+E)">
                <div class="cms-switch"></div>
                <span class="cms-toggle-label" id="cms-toggle-text">Edit Mode: OFF</span>
            </div>

            <div class="cms-full-controls" style="display:flex;align-items:center;gap:8px;">
                <div class="cms-divider"></div>
                
                <button type="button" class="cms-btn cms-btn-primary" id="cms-btn-save" title="Save changes to server (Ctrl+S)" style="display:none;">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                    <span class="cms-badge-dirty" id="cms-dirty-badge" style="display:none;">0</span>
                </button>

                <button type="button" class="cms-btn cms-btn-secondary" id="cms-btn-discard" title="Discard unsaved changes and reload" style="display:none;">
                    <i class="fas fa-undo"></i>
                    <span>Discard</span>
                </button>

                <button type="button" class="cms-btn-icon" id="cms-btn-revisions" title="View previous backups & revisions">
                    <i class="fas fa-history"></i>
                </button>

                <a href="/admin/dashboard" class="cms-btn-icon" title="Go to Admin Dashboard" target="_blank">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </div>

            <button type="button" class="cms-btn-icon" id="cms-btn-minimize" title="Minimize/Maximize toolbar">
                <i class="fas fa-chevron-down" id="cms-min-icon"></i>
            </button>
        `;
        document.body.appendChild(bar);

        // Rich text formatting bubble
        const fmtBar = document.createElement('div');
        fmtBar.id = 'falcon-cms-format-bar';
        fmtBar.innerHTML = `
            <button type="button" data-cmd="bold" title="Bold (Ctrl+B)"><i class="fas fa-bold"></i></button>
            <button type="button" data-cmd="italic" title="Italic (Ctrl+I)"><i class="fas fa-italic"></i></button>
            <button type="button" data-cmd="underline" title="Underline (Ctrl+U)"><i class="fas fa-underline"></i></button>
            <div class="fmt-sep"></div>
            <div class="cms-color-dot" style="background:#C8102E;" data-color="#C8102E" title="Falcon Red"></div>
            <div class="cms-color-dot" style="background:#111827;" data-color="#111827" title="Dark Slate"></div>
            <div class="cms-color-dot" style="background:#ffffff;border-color:#666;" data-color="#ffffff" title="White"></div>
            <div class="cms-color-dot" style="background:#64748b;" data-color="#64748b" title="Muted Gray"></div>
            <div class="fmt-sep"></div>
            <button type="button" id="cms-fmt-link" title="Insert / Edit Link"><i class="fas fa-link"></i></button>
            <button type="button" data-cmd="removeFormat" title="Clear Formatting"><i class="fas fa-remove-format"></i></button>
        `;
        document.body.appendChild(fmtBar);

        // Image Modal
        const imgModal = document.createElement('div');
        imgModal.id = 'falcon-cms-img-modal';
        imgModal.className = 'cms-modal-overlay';
        imgModal.innerHTML = `
            <div class="cms-modal-box">
                <div class="cms-modal-header">
                    <h3><i class="fas fa-image" style="color:#C8102E;"></i> Replace Image</h3>
                    <button type="button" class="cms-modal-close" data-close>&times;</button>
                </div>
                <div class="cms-modal-body">
                    <div class="cms-form-group">
                        <label>Upload New Image From Computer</label>
                        <div class="cms-dropzone" id="cms-img-dropzone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div style="font-weight:600;font-size:13px;margin-bottom:4px;">Drag & drop image here or click to browse</div>
                            <div style="font-size:11px;color:#64748b;">Supports PNG, JPG, WEBP, SVG, GIF</div>
                            <input type="file" id="cms-img-file-input" accept="image/*" style="display:none;">
                        </div>
                    </div>
                    
                    <div class="cms-form-group">
                        <label>Or Enter Image URL / Path</label>
                        <input type="text" class="cms-input" id="cms-img-url-input" placeholder="frontend/images/... or https://...">
                    </div>

                    <div class="cms-form-group">
                        <label>Image Alt / Description</label>
                        <input type="text" class="cms-input" id="cms-img-alt-input" placeholder="Chemical manufacturing...">
                    </div>

                    <div class="cms-preview-box" id="cms-img-preview-box">
                        <div style="font-size:11px;color:#64748b;margin-bottom:6px;">Current Preview</div>
                        <img id="cms-img-preview" src="" alt="Preview">
                    </div>
                </div>
                <div class="cms-modal-footer">
                    <button type="button" class="cms-btn cms-btn-secondary" data-close>Cancel</button>
                    <button type="button" class="cms-btn cms-btn-primary" id="cms-img-apply-btn">Apply Image</button>
                </div>
            </div>
        `;
        document.body.appendChild(imgModal);

        // Link / Button Modal
        const linkModal = document.createElement('div');
        linkModal.id = 'falcon-cms-link-modal';
        linkModal.className = 'cms-modal-overlay';
        linkModal.innerHTML = `
            <div class="cms-modal-box">
                <div class="cms-modal-header">
                    <h3><i class="fas fa-link" style="color:#C8102E;"></i> Edit Link & Button</h3>
                    <button type="button" class="cms-modal-close" data-close>&times;</button>
                </div>
                <div class="cms-modal-body">
                    <div class="cms-form-group">
                        <label>Display Text</label>
                        <input type="text" class="cms-input" id="cms-link-text-input" placeholder="e.g. Learn More">
                    </div>
                    <div class="cms-form-group">
                        <label>Destination Link (URL / Page)</label>
                        <input type="text" class="cms-input" id="cms-link-url-input" placeholder="e.g. /about, contact.html, or https://...">
                    </div>
                    <div class="cms-form-group" style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                        <input type="checkbox" id="cms-link-blank-input" style="width:16px;height:16px;cursor:pointer;">
                        <label for="cms-link-blank-input" style="margin:0;cursor:pointer;text-transform:none;font-weight:normal;font-size:13px;">
                            Open in a new tab / window
                        </label>
                    </div>
                </div>
                <div class="cms-modal-footer">
                    <button type="button" class="cms-btn cms-btn-secondary" data-close>Cancel</button>
                    <button type="button" class="cms-btn cms-btn-primary" id="cms-link-apply-btn">Update Link</button>
                </div>
            </div>
        `;
        document.body.appendChild(linkModal);

        // Revisions Modal
        const revModal = document.createElement('div');
        revModal.id = 'falcon-cms-revisions-modal';
        revModal.className = 'cms-modal-overlay';
        revModal.innerHTML = `
            <div class="cms-modal-box" style="max-width:640px;">
                <div class="cms-modal-header">
                    <h3><i class="fas fa-history" style="color:#C8102E;"></i> Page Backups & Revisions</h3>
                    <button type="button" class="cms-modal-close" data-close>&times;</button>
                </div>
                <div class="cms-modal-body">
                    <div style="font-size:13px;color:#475569;margin-bottom:14px;">
                        Every time you save, a safe backup is automatically recorded. You can restore any past version with one click.
                    </div>
                    <div id="cms-revisions-list">
                        <div style="text-align:center;padding:20px;color:#94a3b8;">
                            <i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><br>Loading backups...
                        </div>
                    </div>
                </div>
                <div class="cms-modal-footer">
                    <button type="button" class="cms-btn cms-btn-secondary" data-close>Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(revModal);

        // Toast alert
        const toast = document.createElement('div');
        toast.id = 'falcon-cms-toast';
        toast.innerHTML = '<i class="fas fa-check-circle" id="cms-toast-icon"></i> <span id="cms-toast-msg">Saved!</span>';
        document.body.appendChild(toast);

        setupEvents();
    }

    // ── Toast Helper ──
    function showToast(message, type = 'success') {
        const toast = document.getElementById('falcon-cms-toast');
        const icon = document.getElementById('cms-toast-icon');
        const msg = document.getElementById('cms-toast-msg');

        toast.className = type;
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        msg.textContent = message;

        toast.style.display = 'flex';
        clearTimeout(toast.__timer);
        toast.__timer = setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // ── Mark Dirty State ──
    function markDirty() {
        dirtyCount++;
        const saveBtn = document.getElementById('cms-btn-save');
        const discardBtn = document.getElementById('cms-btn-discard');
        const badge = document.getElementById('cms-dirty-badge');

        if (saveBtn) {
            saveBtn.style.display = 'inline-flex';
            saveBtn.classList.add('dirty');
        }
        if (discardBtn) {
            discardBtn.style.display = 'inline-flex';
        }
        if (badge) {
            badge.style.display = 'inline-block';
            badge.textContent = dirtyCount;
        }
    }

    function resetDirty() {
        dirtyCount = 0;
        const saveBtn = document.getElementById('cms-btn-save');
        const discardBtn = document.getElementById('cms-btn-discard');
        const badge = document.getElementById('cms-dirty-badge');

        if (saveBtn) {
            saveBtn.classList.remove('dirty');
        }
        if (badge) {
            badge.style.display = 'none';
            badge.textContent = '0';
        }
    }

    // ── Setup Editable Nodes ──
    function attachEditableAttributes() {
        const textSelectors = [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p',
            '.heading__title', '.heading__desc', '.slide__title', '.slide__desc',
            '.footer__copyright', '.contact__desc', '.service__content',
            'blockquote', 'figcaption', '.accordion__item-title',
            'li > span', 'li > strong', 'li > a'
        ];

        document.querySelectorAll(textSelectors.join(', ')).forEach(el => {
            if (el.closest('#falcon-cms-bar, #falcon-cms-format-bar, .cms-modal-overlay')) return;
            el.classList.add('cms-editable-text');
            el.setAttribute('contenteditable', 'true');
            el.setAttribute('spellcheck', 'false');

            if (!el.__cms_listener) {
                el.__cms_listener = true;
                el.addEventListener('input', () => {
                    el.setAttribute('data-cms-modified', 'true');
                    markDirty();
                });
                el.addEventListener('focus', () => {
                    el.setAttribute('data-cms-active-edit', 'true');
                });
                el.addEventListener('blur', () => {
                    el.removeAttribute('data-cms-active-edit');
                });
            }
        });

        // Make all images editable
        document.querySelectorAll('img').forEach(img => {
            if (img.closest('#falcon-cms-bar, #falcon-cms-format-bar, .cms-modal-overlay')) return;
            img.classList.add('cms-editable-image');
            img.setAttribute('title', 'Click to replace image');

            if (!img.__cms_listener) {
                img.__cms_listener = true;
                img.addEventListener('click', (e) => {
                    if (!isEditing) return;
                    e.preventDefault();
                    e.stopPropagation();
                    openImageModal(img);
                });
            }
        });

        // Intercept links while in edit mode
        document.querySelectorAll('a').forEach(link => {
            if (link.closest('#falcon-cms-bar, #falcon-cms-format-bar, .cms-modal-overlay')) return;

            if (!link.__cms_listener) {
                link.__cms_listener = true;
                link.addEventListener('click', (e) => {
                    if (!isEditing) return;
                    // If user is editing text inside, let normal focus work unless clicking border/link
                    if (e.target === link || link.classList.contains('btn') || link.querySelector('img')) {
                        e.preventDefault();
                        e.stopPropagation();
                        openLinkModal(link);
                    }
                });
            }
        });
    }

    function removeEditableAttributes() {
        document.querySelectorAll('.cms-editable-text').forEach(el => {
            el.removeAttribute('contenteditable');
        });
        hideFormatBar();
    }

    // ── Toggle Edit Mode ──
    function toggleEditMode(force) {
        isEditing = typeof force === 'boolean' ? force : !isEditing;
        const toggleGroup = document.getElementById('cms-toggle-edit');
        const toggleLabel = document.getElementById('cms-toggle-text');
        const saveBtn = document.getElementById('cms-btn-save');
        const discardBtn = document.getElementById('cms-btn-discard');

        if (isEditing) {
            document.body.classList.add('cms-editing-active');
            toggleGroup.classList.add('active');
            toggleLabel.textContent = 'Edit Mode: ON';
            saveBtn.style.display = 'inline-flex';
            discardBtn.style.display = 'inline-flex';
            attachEditableAttributes();
            showToast('Visual Edit Mode ON: Click any text to type or image to replace!');
        } else {
            document.body.classList.remove('cms-editing-active');
            toggleGroup.classList.remove('active');
            toggleLabel.textContent = 'Edit Mode: OFF';
            removeEditableAttributes();
            if (dirtyCount === 0) {
                saveBtn.style.display = 'none';
                discardBtn.style.display = 'none';
            }
        }
    }

    // ── Floating Text Format Toolbar ──
    function setupFormatBar() {
        const fmtBar = document.getElementById('falcon-cms-format-bar');

        // Selection change positioning
        document.addEventListener('selectionchange', () => {
            if (!isEditing) return;
            const sel = window.getSelection();
            if (!sel || sel.isCollapsed || !sel.rangeCount) {
                hideFormatBar();
                return;
            }

            const range = sel.getRangeAt(0);
            const commonAncestor = range.commonAncestorContainer;
            const editableParent = commonAncestor.nodeType === 1 ? commonAncestor.closest('.cms-editable-text') : commonAncestor.parentElement.closest('.cms-editable-text');

            if (!editableParent) {
                hideFormatBar();
                return;
            }

            const rect = range.getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) {
                hideFormatBar();
                return;
            }

            fmtBar.style.display = 'flex';
            const top = rect.top + window.scrollY - 44;
            const left = rect.left + window.scrollX + (rect.width / 2) - (fmtBar.offsetWidth / 2);

            fmtBar.style.top = Math.max(10, top) + 'px';
            fmtBar.style.left = Math.max(10, left) + 'px';
        });

        // Command buttons
        fmtBar.querySelectorAll('button[data-cmd]').forEach(btn => {
            btn.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const cmd = btn.getAttribute('data-cmd');
                document.execCommand(cmd, false, null);
                markDirty();
            });
        });

        // Color swatches
        fmtBar.querySelectorAll('.cms-color-dot').forEach(dot => {
            dot.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const color = dot.getAttribute('data-color');
                document.execCommand('foreColor', false, color);
                markDirty();
            });
        });

        // Link button in format bar
        const fmtLinkBtn = document.getElementById('cms-fmt-link');
        if (fmtLinkBtn) {
            fmtLinkBtn.addEventListener('mousedown', (e) => {
                e.preventDefault();
                const sel = window.getSelection();
                const currentLink = sel.anchorNode?.parentElement?.closest('a');
                const defaultUrl = currentLink ? currentLink.href : 'https://';
                const url = prompt('Enter link URL (e.g. /about or https://...):', defaultUrl);
                if (url) {
                    document.execCommand('createLink', false, url);
                    markDirty();
                }
            });
        }
    }

    function hideFormatBar() {
        const fmtBar = document.getElementById('falcon-cms-format-bar');
        if (fmtBar) fmtBar.style.display = 'none';
    }

    // ── Image Modal Logic ──
    function openImageModal(img) {
        activeImageElement = img;
        const modal = document.getElementById('falcon-cms-img-modal');
        const urlInput = document.getElementById('cms-img-url-input');
        const altInput = document.getElementById('cms-img-alt-input');
        const preview = document.getElementById('cms-img-preview');

        urlInput.value = img.getAttribute('src') || '';
        altInput.value = img.getAttribute('alt') || '';
        preview.src = img.src;

        modal.classList.add('active');
    }

    function setupImageModal() {
        const modal = document.getElementById('falcon-cms-img-modal');
        const dropzone = document.getElementById('cms-img-dropzone');
        const fileInput = document.getElementById('cms-img-file-input');
        const urlInput = document.getElementById('cms-img-url-input');
        const altInput = document.getElementById('cms-img-alt-input');
        const preview = document.getElementById('cms-img-preview');
        const applyBtn = document.getElementById('cms-img-apply-btn');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleFileUpload(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                handleFileUpload(fileInput.files[0]);
            }
        });

        urlInput.addEventListener('input', () => {
            preview.src = urlInput.value;
        });

        function handleFileUpload(file) {
            const formData = new FormData();
            formData.append('cms_image', file);

            dropzone.innerHTML = '<i class="fas fa-spinner fa-spin"></i><div style="font-size:12px;font-weight:600;margin-top:6px;">Uploading image to server...</div>';

            fetch('/admin/api/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                dropzone.innerHTML = '<i class="fas fa-check" style="color:#10b981;"></i><div style="font-size:12px;font-weight:600;color:#10b981;">Upload complete!</div>';
                if (data.success && data.url) {
                    urlInput.value = data.url;
                    preview.src = data.url;
                    showToast('Image uploaded successfully!');
                } else {
                    showToast('Upload failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(err => {
                dropzone.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><div style="font-size:12px;">Click to retry upload</div>';
                showToast('Upload error: ' + err.message, 'error');
            });
        }

        applyBtn.addEventListener('click', () => {
            if (activeImageElement) {
                if (urlInput.value) {
                    activeImageElement.src = urlInput.value;
                    activeImageElement.setAttribute('src', urlInput.value);
                }
                if (altInput.value) {
                    activeImageElement.alt = altInput.value;
                    activeImageElement.setAttribute('alt', altInput.value);
                }
                activeImageElement.setAttribute('data-cms-modified', 'true');
                markDirty();
                showToast('Image updated!');
            }
            modal.classList.remove('active');
        });
    }

    // ── Link / Button Modal Logic ──
    function openLinkModal(link) {
        activeLinkElement = link;
        const modal = document.getElementById('falcon-cms-link-modal');
        const textInput = document.getElementById('cms-link-text-input');
        const urlInput = document.getElementById('cms-link-url-input');
        const blankInput = document.getElementById('cms-link-blank-input');

        textInput.value = link.innerText.trim();
        urlInput.value = link.getAttribute('href') || '';
        blankInput.checked = link.getAttribute('target') === '_blank';

        modal.classList.add('active');
    }

    function setupLinkModal() {
        const modal = document.getElementById('falcon-cms-link-modal');
        const textInput = document.getElementById('cms-link-text-input');
        const urlInput = document.getElementById('cms-link-url-input');
        const blankInput = document.getElementById('cms-link-blank-input');
        const applyBtn = document.getElementById('cms-link-apply-btn');

        applyBtn.addEventListener('click', () => {
            if (activeLinkElement) {
                if (textInput.value.trim() && !activeLinkElement.querySelector('img')) {
                    // Update text without destroying inner icons if possible
                    const icon = activeLinkElement.querySelector('i');
                    if (icon) {
                        activeLinkElement.innerHTML = '';
                        activeLinkElement.appendChild(icon);
                        activeLinkElement.appendChild(document.createTextNode(' ' + textInput.value.trim()));
                    } else {
                        activeLinkElement.textContent = textInput.value.trim();
                    }
                }
                activeLinkElement.setAttribute('href', urlInput.value.trim() || '#');
                if (blankInput.checked) {
                    activeLinkElement.setAttribute('target', '_blank');
                    activeLinkElement.setAttribute('rel', 'noopener noreferrer');
                } else {
                    activeLinkElement.removeAttribute('target');
                    activeLinkElement.removeAttribute('rel');
                }
                activeLinkElement.setAttribute('data-cms-modified', 'true');
                markDirty();
                showToast('Link & button updated!');
            }
            modal.classList.remove('active');
        });
    }

    // ── Revisions Modal Logic ──
    function openRevisionsModal() {
        const modal = document.getElementById('falcon-cms-revisions-modal');
        const list = document.getElementById('cms-revisions-list');
        modal.classList.add('active');

        list.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size:20px;"></i> Loading backups...</div>';

        fetch('/admin/api/page-backups/' + encodeURIComponent(config.pageName))
            .then(res => res.json())
            .then(data => {
                if (!data.backups || data.backups.length === 0) {
                    list.innerHTML = `
                        <div style="text-align:center;padding:30px;color:#64748b;">
                            <i class="fas fa-shield-alt" style="font-size:36px;color:#cbd5e1;margin-bottom:8px;"></i>
                            <p style="margin:0;font-weight:600;">No backups created yet.</p>
                            <p style="margin:4px 0 0;font-size:12px;">A backup will be created automatically the first time you click "Save Changes".</p>
                        </div>
                    `;
                    return;
                }

                let html = '<table class="cms-backups-table"><thead><tr><th>Date & Time</th><th>File Size</th><th>Action</th></tr></thead><tbody>';
                data.backups.forEach(b => {
                    html += `
                        <tr>
                            <td><strong>${b.formattedDate}</strong><div style="font-size:11px;color:#64748b;">${b.filename}</div></td>
                            <td>${(b.size / 1024).toFixed(1)} KB</td>
                            <td>
                                <button type="button" class="cms-btn cms-btn-secondary btn-restore" data-file="${b.filename}" style="padding:4px 10px;font-size:11px;">
                                    <i class="fas fa-history"></i> Restore
                                </button>
                            </td>
                        </tr>
                    `;
                });
                html += '</tbody></table>';
                list.innerHTML = html;

                list.querySelectorAll('.btn-restore').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const filename = btn.getAttribute('data-file');
                        if (confirm(`Restore version from "${btn.closest('tr').querySelector('strong').textContent}"?\nYour current page will be updated.`)) {
                            fetch('/admin/api/restore-backup', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ page: config.pageName, backupFile: filename })
                            })
                            .then(res => res.json())
                            .then(resData => {
                                if (resData.success) {
                                    alert('Version restored successfully! Reloading page...');
                                    window.location.reload();
                                } else {
                                    alert('Failed to restore: ' + resData.message);
                                }
                            });
                        }
                    });
                });
            })
            .catch(err => {
                list.innerHTML = '<div style="color:#ef4444;padding:15px;">Failed to load backups: ' + err.message + '</div>';
            });
    }

    // ── Save Changes to Server ──
    function savePage() {
        const saveBtn = document.getElementById('cms-btn-save');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';

        // Blur any active element
        if (document.activeElement) document.activeElement.blur();
        hideFormatBar();

        // Prepare clean HTML copy:
        // We clone documentElement so we can clean attributes without disrupting current DOM
        const docClone = document.documentElement.cloneNode(true);

        // Remove our injected CMS elements from the clone so they don't get saved into the HTML file!
        const cmsSelectors = [
            '#falcon-cms-bar',
            '#falcon-cms-format-bar',
            '#falcon-cms-img-modal',
            '#falcon-cms-link-modal',
            '#falcon-cms-revisions-modal',
            '#falcon-cms-toast',
            'link[href*="visual-editor.css"]',
            'script[src*="visual-editor.js"]',
            'style#falcon-cms-style',
            'script:not([src])' // We'll handle inline CMS script below
        ];

        // Specifically remove our injection block and scripts
        docClone.querySelectorAll('#falcon-cms-bar, #falcon-cms-format-bar, .cms-modal-overlay, #falcon-cms-toast').forEach(el => el.remove());
        docClone.querySelectorAll('link[href*="visual-editor.css"], script[src*="visual-editor.js"]').forEach(el => el.remove());
        
        // Remove script tags with __FALCON_CMS__
        docClone.querySelectorAll('script').forEach(s => {
            if (s.textContent.includes('__FALCON_CMS__')) s.remove();
        });

        // Clean body classes and helper attributes from elements
        docClone.classList.remove('cms-editing-active');
        const bodyClone = docClone.querySelector('body');
        if (bodyClone) bodyClone.classList.remove('cms-editing-active');

        docClone.querySelectorAll('.cms-editable-text, .cms-editable-image').forEach(el => {
            el.classList.remove('cms-editable-text', 'cms-editable-image');
            el.removeAttribute('contenteditable');
            el.removeAttribute('spellcheck');
            el.removeAttribute('data-cms-modified');
            el.removeAttribute('data-cms-active-edit');
            if (el.getAttribute('title') === 'Click to replace image') el.removeAttribute('title');
        });

        const cleanHtml = '<!DOCTYPE html>\n' + docClone.outerHTML;

        fetch('/admin/api/save-page', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                page: config.pageName,
                html: cleanHtml
            })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> <span>Save Changes</span> <span class="cms-badge-dirty" id="cms-dirty-badge" style="display:none;">0</span>';

            if (data.success) {
                resetDirty();
                showToast('✓ ' + (data.message || 'Page saved successfully! Changes are live.'));
            } else {
                showToast('Save failed: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> <span>Save Changes</span>';
            showToast('Save error: ' + err.message, 'error');
        });
    }

    // ── Setup DOM Events ──
    function setupEvents() {
        // Toggle Edit Mode button
        document.getElementById('cms-toggle-edit').addEventListener('click', () => {
            toggleEditMode();
        });

        // Save button
        document.getElementById('cms-btn-save').addEventListener('click', () => {
            savePage();
        });

        // Discard button
        document.getElementById('cms-btn-discard').addEventListener('click', () => {
            if (confirm('Discard all unsaved edits and reload the page?')) {
                window.location.reload();
            }
        });

        // Revisions button
        document.getElementById('cms-btn-revisions').addEventListener('click', () => {
            openRevisionsModal();
        });

        // Minimize toolbar toggle
        document.getElementById('cms-btn-minimize').addEventListener('click', () => {
            const bar = document.getElementById('falcon-cms-bar');
            const icon = document.getElementById('cms-min-icon');
            bar.classList.toggle('minimized');
            if (bar.classList.contains('minimized')) {
                icon.className = 'fas fa-chevron-up';
            } else {
                icon.className = 'fas fa-chevron-down';
            }
        });

        // Close modal buttons
        document.querySelectorAll('.cms-modal-overlay [data-close]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.cms-modal-overlay').classList.remove('active');
            });
        });

        // Close modal on background click
        document.querySelectorAll('.cms-modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('active');
            });
        });

        // Setup format bar and modals
        setupFormatBar();
        setupImageModal();
        setupLinkModal();

        // Keyboard shortcuts
        window.addEventListener('keydown', (e) => {
            // Ctrl+E or Cmd+E to toggle edit mode
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'e') {
                e.preventDefault();
                toggleEditMode();
            }
            // Ctrl+S or Cmd+S to save
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                e.preventDefault();
                if (isEditing || dirtyCount > 0) {
                    savePage();
                }
            }
            // Escape to close modals
            if (e.key === 'Escape') {
                document.querySelectorAll('.cms-modal-overlay.active').forEach(m => m.classList.remove('active'));
            }
        });

        // Auto-start if requested via ?edit=1
        if (config.autoStart) {
            setTimeout(() => {
                toggleEditMode(true);
            }, 300);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUI);
    } else {
        initUI();
    }
})();
