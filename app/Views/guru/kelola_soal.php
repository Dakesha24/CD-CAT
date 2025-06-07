<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <div class="row mb-4 align-items-center py-5">
        <div class="col">
            <h2 class="fw-bold text-primary"><?= $ujian['nama_ujian'] ?></h2>
            <p class="text-muted">Kelola Soal Ujian</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahSoalModal">
                <i class="fas fa-plus me-2"></i>Tambah Soal
            </button>
        </div>
    </div>

    <!-- Info Box untuk Panduan Rumus -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Panduan Menulis Rumus Matematika</h6>
        <p class="mb-2">Untuk menulis rumus matematika, gunakan format LaTeX dengan tanda dollar ($):</p>
        <ul class="mb-2">
            <li><strong>Inline:</strong> <code>$x^2 + y^2 = z^2$</code> → $x^2 + y^2 = z^2$</li>
            <li><strong>Block:</strong> <code>$$\frac{-b \pm \sqrt{b^2-4ac}}{2a}$$</code> → rumus kuadrat dalam blok</li>
            <li><strong>Pecahan:</strong> <code>$\frac{a}{b}$</code> → $\frac{a}{b}$</li>
            <li><strong>Akar:</strong> <code>$\sqrt{x}$</code> → $\sqrt{x}$</li>
            <li><strong>Subscript/Superscript:</strong> <code>$H_2O$, $E=mc^2$</code> → $H_2O$, $E=mc^2$</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4" width="5%">No</th>
                            <th width="20%">Pertanyaan</th>
                            <th width="10%">Foto</th>
                            <th width="20%">Pilihan</th>
                            <th width="10%">Jawaban</th>
                            <th width="10%">Kesulitan</th>
                            <th width="10%">Pembahasan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($soal as $s): ?>
                            <tr>
                                <td class="px-4"><?= $i++ ?></td>
                                <td class="math-content"><?= $s['pertanyaan'] ?></td>
                                <td>
                                    <?php if (!empty($s['foto'])): ?>
                                        <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 80px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <small class="math-content"><span class="fw-bold">A.</span> <?= $s['pilihan_a'] ?></small>
                                        <small class="math-content"><span class="fw-bold">B.</span> <?= $s['pilihan_b'] ?></small>
                                        <small class="math-content"><span class="fw-bold">C.</span> <?= $s['pilihan_c'] ?></small>
                                        <small class="math-content"><span class="fw-bold">D.</span> <?= $s['pilihan_d'] ?></small>
                                        <?php if (!empty($s['pilihan_e'])): ?>
                                            <small class="math-content"><span class="fw-bold">E.</span> <?= $s['pilihan_e'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center fw-bold"><?= $s['jawaban_benar'] ?></td>
                                <td><?= $s['tingkat_kesulitan'] ?></td>
                                <td>
                                    <?php if (!empty($s['pembahasan'])): ?>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#pembahasanModal<?= $s['soal_id'] ?>">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSoalModal<?= $s['soal_id'] ?>">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </button>
                                        <a href="<?= base_url('guru/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah anda yakin?')">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembahasan -->
<?php foreach ($soal as $s):
    if (!empty($s['pembahasan'])): ?>
        <div class="modal fade" id="pembahasanModal<?= $s['soal_id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Pembahasan Soal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold mb-2">Pertanyaan:</p>
                        <p class="math-content"><?= $s['pertanyaan'] ?></p>

                        <?php if (!empty($s['foto'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-fluid" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>

                        <p class="fw-bold mb-2">Jawaban Benar: <?= $s['jawaban_benar'] ?></p>

                        <div class="card bg-light">
                            <div class="card-header fw-bold">Pembahasan</div>
                            <div class="card-body math-content">
                                <?= nl2br(esc($s['pembahasan'])) ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
<?php endif;
endforeach; ?>

<!-- Modal Tambah Soal -->
<div class="modal fade" id="tambahSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Soal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/soal/tambah') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control math-preview" rows="4" required placeholder="Masukkan pertanyaan. Gunakan $...$ untuk rumus matematika. Contoh: Berapa nilai dari $x^2 + 2x - 8 = 0$?"></textarea>
                            <div class="mt-2 p-2 border rounded bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="preview-pertanyaan" class="math-content mt-1"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Foto Soal (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Upload gambar dengan format JPG, JPEG, atau PNG (maks. 2MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilihan A</label>
                            <textarea name="pilihan_a" class="form-control math-preview" rows="2" required placeholder="$\frac{1}{2}$ atau teks biasa"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-pilihan-a" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan B</label>
                            <textarea name="pilihan_b" class="form-control math-preview" rows="2" required placeholder="$\sqrt{2}$ atau teks biasa"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-pilihan-b" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan C</label>
                            <textarea name="pilihan_c" class="form-control math-preview" rows="2" required placeholder="$x^2$ atau teks biasa"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-pilihan-c" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan D</label>
                            <textarea name="pilihan_d" class="form-control math-preview" rows="2" required placeholder="$2\pi$ atau teks biasa"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-pilihan-d" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan E (Opsional)</label>
                            <textarea name="pilihan_e" class="form-control math-preview" rows="2" placeholder="$\log_2 8$ atau teks biasa"></textarea>
                            <div class="mt-1 p-1 border rounded bg-light">
                                <div id="preview-pilihan-e" class="math-content small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jawaban Benar</label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="">Pilih Jawaban Benar</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat Kesulitan [-3 hingga +3]</label>
                            <input type="number" name="tingkat_kesulitan" class="form-control" step="0.0001" value="0.0000" min="-3" max="3" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Pembahasan (Opsional)</label>
                            <textarea name="pembahasan" class="form-control math-preview" rows="4" placeholder="Masukkan pembahasan. Contoh: Untuk menyelesaikan $x^2 + 2x - 8 = 0$, kita gunakan rumus kuadrat: $$x = \frac{-b \pm \sqrt{b^2-4ac}}{2a}$$"></textarea>
                            <div class="mt-2 p-2 border rounded bg-light">
                                <small class="text-muted">Preview:</small>
                                <div id="preview-pembahasan" class="math-content mt-1"></div>
                            </div>
                            <small class="text-muted">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Soal -->
<?php foreach ($soal as $s): ?>
    <div class="modal fade" id="editSoalModal<?= $s['soal_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Edit Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('guru/soal/edit/' . $s['soal_id']) ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pertanyaan</label>
                                <textarea name="pertanyaan" class="form-control math-preview" rows="4" required><?= $s['pertanyaan'] ?></textarea>
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <small class="text-muted">Preview:</small>
                                    <div id="preview-pertanyaan-edit-<?= $s['soal_id'] ?>" class="math-content mt-1"><?= $s['pertanyaan'] ?></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Foto Soal (Opsional)</label>
                                <?php if (!empty($s['foto'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapusFoto<?= $s['soal_id'] ?>" value="1">
                                        <label class="form-check-label" for="hapusFoto<?= $s['soal_id'] ?>">
                                            Hapus foto
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Upload gambar baru dengan format JPG, JPEG, atau PNG (maks. 2MB)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pilihan A</label>
                                <textarea name="pilihan_a" class="form-control math-preview" rows="2" required><?= $s['pilihan_a'] ?></textarea>
                                <div class="mt-1 p-1 border rounded bg-light">
                                    <div id="preview-pilihan-a-edit-<?= $s['soal_id'] ?>" class="math-content small"><?= $s['pilihan_a'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan B</label>
                                <textarea name="pilihan_b" class="form-control math-preview" rows="2" required><?= $s['pilihan_b'] ?></textarea>
                                <div class="mt-1 p-1 border rounded bg-light">
                                    <div id="preview-pilihan-b-edit-<?= $s['soal_id'] ?>" class="math-content small"><?= $s['pilihan_b'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan C</label>
                                <textarea name="pilihan_c" class="form-control math-preview" rows="2" required><?= $s['pilihan_c'] ?></textarea>
                                <div class="mt-1 p-1 border rounded bg-light">
                                    <div id="preview-pilihan-c-edit-<?= $s['soal_id'] ?>" class="math-content small"><?= $s['pilihan_c'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan D</label>
                                <textarea name="pilihan_d" class="form-control math-preview" rows="2" required><?= $s['pilihan_d'] ?></textarea>
                                <div class="mt-1 p-1 border rounded bg-light">
                                    <div id="preview-pilihan-d-edit-<?= $s['soal_id'] ?>" class="math-content small"><?= $s['pilihan_d'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan E (Opsional)</label>
                                <textarea name="pilihan_e" class="form-control math-preview" rows="2"><?= isset($s['pilihan_e']) ? esc($s['pilihan_e']) : '' ?></textarea>
                                <div class="mt-1 p-1 border rounded bg-light">
                                    <div id="preview-pilihan-e-edit-<?= $s['soal_id'] ?>" class="math-content small"><?= isset($s['pilihan_e']) ? $s['pilihan_e'] : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jawaban Benar</label>
                                <select name="jawaban_benar" class="form-select" required>
                                    <option value="">Pilih Jawaban Benar</option>
                                    <option value="A" <?= $s['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= $s['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="C" <?= $s['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                                    <option value="D" <?= $s['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                                    <option value="E" <?= $s['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tingkat Kesulitan [-3 hingga +3]</label>
                                <input type="number" name="tingkat_kesulitan" class="form-control" step="0.0001" value="<?= $s['tingkat_kesulitan'] ?>" min="-3" max="3" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pembahasan (Opsional)</label>
                                <textarea name="pembahasan" class="form-control math-preview" rows="4"><?= isset($s['pembahasan']) ? esc($s['pembahasan']) : '' ?></textarea>
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <small class="text-muted">Preview:</small>
                                    <div id="preview-pembahasan-edit-<?= $s['soal_id'] ?>" class="math-content mt-1"><?= isset($s['pembahasan']) ? $s['pembahasan'] : '' ?></div>
                                </div>
                                <small class="text-muted">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Load MathJax -->
<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$']],
        displayMath: [['$$', '$$']],
        processEscapes: true,
        processEnvironments: true
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
        ignoreHtmlClass: 'tex2jax_ignore',
        processHtmlClass: 'math-content'
    },
    startup: {
        ready: function () {
            MathJax.startup.defaultReady();
            
            // Setup preview functionality
            setupMathPreview();
        }
    }
};
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
function setupMathPreview() {
    // Function to update preview
    function updatePreview(textarea, previewId) {
        const content = textarea.value;
        const previewElement = document.getElementById(previewId);
        if (previewElement) {
            previewElement.innerHTML = content;
            MathJax.typesetPromise([previewElement]).catch((err) => console.log(err));
        }
    }
    
    // Add event listeners for all math-preview textareas
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('math-preview')) {
            const textarea = e.target;
            let previewId = '';
            
            // Determine preview ID based on textarea name
            const name = textarea.name;
            if (name === 'pertanyaan') {
                previewId = textarea.closest('.modal').id === 'tambahSoalModal' ? 'preview-pertanyaan' : 'preview-pertanyaan-edit-' + textarea.closest('form').querySelector('input[name="ujian_id"]').value;
                // For edit modals, extract soal_id from form action
                if (textarea.closest('.modal').id.includes('editSoalModal')) {
                    const formAction = textarea.closest('form').action;
                    const soalId = formAction.split('/').pop();
                    previewId = 'preview-pertanyaan-edit-' + soalId;
                }
            } else if (name === 'pembahasan') {
                previewId = textarea.closest('.modal').id === 'tambahSoalModal' ? 'preview-pembahasan' : 'preview-pembahasan-edit-' + textarea.closest('form').querySelector('input[name="ujian_id"]').value;
                // For edit modals
                if (textarea.closest('.modal').id.includes('editSoalModal')) {
                    const formAction = textarea.closest('form').action;
                    const soalId = formAction.split('/').pop();
                    previewId = 'preview-pembahasan-edit-' + soalId;
                }
            } else if (name.startsWith('pilihan_')) {
                const pilihan = name.split('_')[1];
                previewId = textarea.closest('.modal').id === 'tambahSoalModal' ? 'preview-pilihan-' + pilihan : 'preview-pilihan-' + pilihan + '-edit-' + textarea.closest('form').querySelector('input[name="ujian_id"]').value;
                // For edit modals
                if (textarea.closest('.modal').id.includes('editSoalModal')) {
                    const formAction = textarea.closest('form').action;
                    const soalId = formAction.split('/').pop();
                    previewId = 'preview-pilihan-' + pilihan + '-edit-' + soalId;
                }
            }
            
            updatePreview(textarea, previewId);
        }
    });
    
    // Initial render for existing content
    setTimeout(() => {
        MathJax.typesetPromise().catch((err) => console.log(err));
    }, 100);
}

// Re-render MathJax when modals are shown
document.addEventListener('shown.bs.modal', function (e) {
    setTimeout(() => {
        MathJax.typesetPromise([e.target]).catch((err) => console.log(err));
    }, 100);
});
</script>

<style>
.math-content {
    min-height: 20px;
}

.math-preview {
    font-family: 'Courier New', monospace;
}

.alert-info ul {
    margin-bottom: 0;
}

.alert-info li {
    margin-bottom: 5px;
}

mjx-container {
    display: inline-block !important;
}
</style>

<?= $this->endSection() ?>