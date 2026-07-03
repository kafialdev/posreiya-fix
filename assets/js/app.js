const DEMO_MODE = new URLSearchParams(window.location.search).get('demo') === '1';

let demoProducts = [
  {id:1,sku:'WRD-BDK-01',name:'Colorfit Velvet Powder Foundation',category:'Bedak',brand:'Wardah',variant:'01 Light Beige',price:89000,stock:24,image:'assets/img/skincare/wardah-bedak-light-beige.svg'},
  {id:2,sku:'WRD-BDK-02',name:'Colorfit Velvet Powder Foundation',category:'Bedak',brand:'Wardah',variant:'02 Natural',price:89000,stock:18,image:'assets/img/skincare/wardah-bedak-natural.svg'},
  {id:3,sku:'MKO-BDK-W22',name:'Powerstay Matte Powder Foundation',category:'Bedak',brand:'Make Over',variant:'W22 Warm Ivory',price:159000,stock:14,image:'assets/img/skincare/makeover-bedak-w22.svg'},
  {id:4,sku:'MKO-BDK-N30',name:'Powerstay Matte Powder Foundation',category:'Bedak',brand:'Make Over',variant:'N30 Natural Beige',price:159000,stock:12,image:'assets/img/skincare/makeover-bedak-n30.svg'},
  {id:5,sku:'EMN-BDK-01',name:'Bare With Me Mineral Compact Powder',category:'Bedak',brand:'Emina',variant:'01 Fair',price:54000,stock:27,image:'assets/img/skincare/emina-bedak-fair.svg'},
  {id:6,sku:'WRD-LIP-01',name:'Colorfit Velvet Matte Lip Mousse',category:'Lipstik',brand:'Wardah',variant:'01 Brown Dreamer',price:79000,stock:32,image:'assets/img/skincare/wardah-lip-brown.svg'},
  {id:7,sku:'WRD-LIP-05',name:'Colorfit Velvet Matte Lip Mousse',category:'Lipstik',brand:'Wardah',variant:'05 Artisan Mauve',price:79000,stock:21,image:'assets/img/skincare/wardah-lip-mauve.svg'},
  {id:8,sku:'HNS-LIP-02',name:'Mattedorable Lip Cream',category:'Lipstik',brand:'Hanasui',variant:'02 Chic',price:35000,stock:36,image:'assets/img/skincare/hanasui-lip-chic.svg'},
  {id:9,sku:'HNS-LIP-04',name:'Mattedorable Lip Cream',category:'Lipstik',brand:'Hanasui',variant:'04 Pink Latte',price:35000,stock:29,image:'assets/img/skincare/hanasui-lip-pink.svg'},
  {id:10,sku:'SKN-SRM-5X',name:'5X Ceramide Barrier Repair Serum',category:'Serum',brand:'Skintific',variant:'20 ml',price:129000,stock:20,image:'assets/img/skincare/skintific-serum-ceramide.svg'},
  {id:11,sku:'SKN-SRM-NIA',name:'10% Niacinamide Brightening Serum',category:'Serum',brand:'Skintific',variant:'20 ml',price:139000,stock:16,image:'assets/img/skincare/skintific-serum-niacinamide.svg'},
  {id:12,sku:'AZR-SUN-HYD',name:'Hydrasoothe Sunscreen Gel SPF45',category:'Sunscreen',brand:'Azarine',variant:'50 ml',price:69000,stock:31,image:'assets/img/skincare/azarine-sunscreen-hydra.svg'},
  {id:13,sku:'AZR-SUN-TUP',name:'Tone Up Mineral Sunscreen Serum SPF50',category:'Sunscreen',brand:'Azarine',variant:'40 ml',price:75000,stock:25,image:'assets/img/skincare/azarine-sunscreen-toneup.svg'},
  {id:14,sku:'EMN-FW-BRT',name:'Bright Stuff Face Wash',category:'Facial Wash',brand:'Emina',variant:'50 ml',price:32000,stock:40,image:'assets/img/skincare/emina-wash-bright.svg'},
  {id:15,sku:'EMN-FW-ACN',name:'Ms. Pimple Acne Solution Face Wash',category:'Facial Wash',brand:'Emina',variant:'50 ml',price:38000,stock:22,image:'assets/img/skincare/emina-wash-pimple.svg'},
  {id:16,sku:'MKO-FND-W22',name:'Powerstay Weightless Liquid Foundation',category:'Foundation',brand:'Make Over',variant:'W22 Warm Ivory',price:189000,stock:13,image:'assets/img/skincare/makeover-foundation-w22.svg'},
  {id:17,sku:'MKO-FND-N30',name:'Powerstay Weightless Liquid Foundation',category:'Foundation',brand:'Make Over',variant:'N30 Natural Beige',price:189000,stock:10,image:'assets/img/skincare/makeover-foundation-n30.svg'},
  {id:18,sku:'WRD-FND-21N',name:'Colorfit Matte Foundation',category:'Foundation',brand:'Wardah',variant:'21N Shell Ivory',price:95000,stock:19,image:'assets/img/skincare/wardah-foundation-shell.svg'}
];

let demoTransactions = [
  {transaction_code:'TRX-260625-121501-21',customer_name:'Nadia',customer_phone:'6281234567890',payment_method:'QRIS',total:218000,paid:218000,change_amount:0,created_at:new Date().toISOString().slice(0,19).replace('T',' '),receipt_token:'demo-1'},
  {transaction_code:'TRX-260625-104210-17',customer_name:'Pelanggan umum',customer_phone:'',payment_method:'Tunai',total:124000,paid:150000,change_amount:26000,created_at:new Date(Date.now()-3600000).toISOString().slice(0,19).replace('T',' '),receipt_token:'demo-2'}
];

async function demoApi(action, options = {}) {
  await new Promise((resolve) => setTimeout(resolve, 90));
  if (action === 'products') return {success:true,products:demoProducts};
  if (action === 'transactions') return {success:true,transactions:demoTransactions};
  if (action === 'dashboard') {
    const revenue = demoTransactions.reduce((sum,item)=>sum+Number(item.total),0);
    const today = new Date();
    const chart = Array.from({length:7},(_,i)=>{
      const d=new Date(today); d.setDate(d.getDate()-(6-i));
      return {sale_date:`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`,revenue:i===6?revenue:[215000,340000,278000,445000,380000,510000][i]||0};
    });
    return {success:true,summary:{revenue_today:revenue,transactions_today:demoTransactions.length,average_order:revenue/demoTransactions.length,active_products:demoProducts.length},recent:demoTransactions,chart};
  }
  if (action === 'delete_product') {
    const body = JSON.parse(options.body || '{}');
    demoProducts = demoProducts.filter((p)=>p.id!==Number(body.id));
    return {success:true,message:'Produk demo dihapus.'};
  }
  if (action === 'save_product') {
    const form = options.body;
    const id = Number(form.get('id') || 0);
    const product = {
      id:id||Math.max(0,...demoProducts.map(p=>p.id))+1,
      sku:String(form.get('sku')||'').toUpperCase(),
      name:form.get('name'), category:form.get('category'), brand:form.get('brand'), variant:form.get('variant'),
      price:Number(form.get('price')), stock:Number(form.get('stock')),
      image:form.get('existing_image')||'assets/img/product-placeholder.svg'
    };
    demoProducts = id ? demoProducts.map((p)=>p.id===id?product:p) : [...demoProducts,product];
    return {success:true,message:id?'Produk demo diperbarui.':'Produk demo ditambahkan.'};
  }
  if (action === 'save_transaction') {
    const body = JSON.parse(options.body || '{}');
    const subtotal = body.items.reduce((sum,item)=>{const p=demoProducts.find(x=>x.id===Number(item.product_id));return sum+(p?p.price*Number(item.quantity):0)},0);
    const total = Math.max(0,subtotal-Number(body.discount||0));
    const code = `TRX-DEMO-${Date.now().toString().slice(-6)}`;
    body.items.forEach((item)=>{const p=demoProducts.find(x=>x.id===Number(item.product_id));if(p)p.stock=Math.max(0,p.stock-Number(item.quantity));});
    demoTransactions.unshift({transaction_code:code,customer_name:body.customer_name||'Pelanggan umum',customer_phone:normalizePhone(body.customer_phone),payment_method:body.payment_method,total,paid:Number(body.paid||total),change_amount:Math.max(0,Number(body.paid||total)-total),created_at:new Date().toISOString().slice(0,19).replace('T',' '),receipt_token:'demo'});
    return {success:true,message:'Transaksi demo berhasil.',transaction_code:code,token:'demo',receipt_url:'#',print_url:'#',download_url:'#',whatsapp_text:`*${window.POS_CONFIG?.storeName||'Toko'}*\nNota demo ${code}\nTotal: ${rupiah(total)}`,phone:normalizePhone(body.customer_phone),total,change:Math.max(0,Number(body.paid||total)-total)};
  }
  throw new Error('Aksi demo tidak tersedia.');
}

const state = {
  products: [],
  cart: new Map(),
  level: 'category',
  selectedCategory: '',
  selectedBrand: '',
  search: '',
  lastTransaction: null,
  currentPage: 'cashier'
};

const categoryOrder = ['Bedak','Foundation','Lipstik','Serum','Sunscreen','Facial Wash'];
const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
const rupiah = (value) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(Number(value || 0));
const escapeHtml = (value = '') => String(value).replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
const safeImage = (value = '') => {
  const path = String(value || '').replace(/\\/g, '/').trim();
  if (!path || path.includes('..') || /[\x00-\x1F\x7F]/.test(path)) return 'assets/img/product-placeholder.svg';
  if (!/^(assets\/img\/|uploads\/)/.test(path)) return 'assets/img/product-placeholder.svg';
  if (!/\.(svg|jpg|jpeg|png|webp)$/i.test(path)) return 'assets/img/product-placeholder.svg';
  return path;
};

async function api(action, options = {}) {
  if (DEMO_MODE) return demoApi(action, options);
  const response = await fetch(`api.php?action=${encodeURIComponent(action)}`, options);
  const payload = await response.json().catch(() => ({success:false,message:'Respons server tidak valid.'}));
  if (!response.ok || !payload.success) throw new Error(payload.message || 'Permintaan gagal.');
  return payload;
}

function toast(message, error = false) {
  const el = $('#toast');
  el.textContent = message;
  el.classList.toggle('error', error);
  el.classList.add('show');
  clearTimeout(window.toastTimer);
  window.toastTimer = setTimeout(() => el.classList.remove('show'), 2600);
}
function openModal(id){const modal=document.getElementById(id);modal.classList.add('open');modal.setAttribute('aria-hidden','false');}
function closeModal(id){const modal=document.getElementById(id);modal.classList.remove('open');modal.setAttribute('aria-hidden','true');}

function setPage(page) {
  state.currentPage = page;
  $$('.page').forEach((el)=>el.classList.toggle('active',el.id===`page-${page}`));
  $$('.nav-item').forEach((el)=>el.classList.toggle('active',el.dataset.page===page));
  const labels = {
    cashier:['POINT OF SALE','Kasir'],
    dashboard:['RINGKASAN BISNIS','Dashboard'],
    products:['MANAJEMEN KATALOG','Produk'],
    transactions:['RIWAYAT PENJUALAN','Transaksi']
  };
  $('#pageEyebrow').textContent=labels[page][0];
  $('#pageTitle').textContent=labels[page][1];
  $('.sidebar').classList.remove('open');
  if(page==='dashboard')loadDashboard();
  if(page==='transactions')loadTransactions();
  if(page==='products')renderProductTable();
}

async function loadProducts() {
  try {
    const payload = await api('products');
    state.products = payload.products.map((p)=>({...p,id:Number(p.id),price:Number(p.price),stock:Number(p.stock),image:safeImage(p.image),brand:p.brand||'Tanpa Merek',variant:p.variant||'-'}));
    for(const [id,cartItem] of state.cart.entries()){
      const product=state.products.find((p)=>p.id===id);
      if(!product||product.stock<=0)state.cart.delete(id); else cartItem.quantity=Math.min(cartItem.quantity,product.stock);
    }
    applyDeepLink();
    renderCatalog();
    renderProductTable();
    renderCart();
  } catch(error) {
    $('#productGrid').innerHTML=`<div class="empty-state"><strong>Produk belum dapat dimuat.</strong><br>${escapeHtml(error.message)}<br><br>Jalankan <b>setup.php</b> dan periksa config.php.</div>`;
    toast(error.message,true);
  }
}

function applyDeepLink(){
  if(state.products.length===0||state._deepLinkApplied)return;
  const params=new URLSearchParams(location.search);
  const category=params.get('category');
  const brand=params.get('brand');
  if(category&&state.products.some((p)=>p.category.toLowerCase()===category.toLowerCase())){
    state.selectedCategory=state.products.find((p)=>p.category.toLowerCase()===category.toLowerCase()).category;
    state.level='brand';
    if(brand&&state.products.some((p)=>p.category===state.selectedCategory&&p.brand.toLowerCase()===brand.toLowerCase())){
      state.selectedBrand=state.products.find((p)=>p.category===state.selectedCategory&&p.brand.toLowerCase()===brand.toLowerCase()).brand;
      state.level='product';
    }
  }
  state._deepLinkApplied=true;
}

function orderedCategories(){
  const categories=[...new Set(state.products.map((p)=>p.category).filter(Boolean))];
  return categories.sort((a,b)=>{
    const ai=categoryOrder.indexOf(a), bi=categoryOrder.indexOf(b);
    if(ai===-1&&bi===-1)return a.localeCompare(b);
    if(ai===-1)return 1;if(bi===-1)return -1;return ai-bi;
  });
}
function categoryProducts(category){return state.products.filter((p)=>p.category===category);}
function brandProducts(category,brand){return state.products.filter((p)=>p.category===category&&p.brand===brand);}
function brandGradient(brand){
  const palettes=[['#f3dce5','#e7bdca'],['#e9dfd8','#c9a99b'],['#dcebe7','#9fcabf'],['#e7e0f0','#bba9d1'],['#f1e3c8','#d7bb77'],['#dce7f3','#9dbbd8']];
  let hash=0;for(const ch of brand)hash=(hash*31+ch.charCodeAt(0))>>>0;return palettes[hash%palettes.length];
}

function setCatalogLevel(level,category='',brand=''){
  state.level=level;state.selectedCategory=category;state.selectedBrand=brand;state.search='';$('#productSearch').value='';renderCatalog();
}

function renderCatalog(){
  const grid=$('#productGrid');
  const query=state.search.trim().toLowerCase();
  if(query){
    const matches=state.products.filter((p)=>`${p.name} ${p.sku} ${p.category} ${p.brand} ${p.variant}`.toLowerCase().includes(query));
    updateCatalogHeader('search',matches.length);
    grid.className='product-grid segment-grid product-view';
    grid.innerHTML=matches.length?matches.map(productCard).join(''):'<div class="empty-state"><strong>Produk tidak ditemukan.</strong><br>Coba kata kunci jenis, merek, shade, atau SKU lain.</div>';
    bindProductCards();return;
  }
  if(state.level==='category'){
    const categories=orderedCategories();
    updateCatalogHeader('category',categories.length);
    grid.className='product-grid segment-grid category-view';
    grid.innerHTML=categories.length?categories.map((category)=>{
      const products=categoryProducts(category);const brands=new Set(products.map((p)=>p.brand));const image=safeImage(products[0]?.image);
      return `<button class="segment-card category-card" data-open-category="${escapeHtml(category)}">
        <div class="segment-visual"><img src="${escapeHtml(image)}" alt="${escapeHtml(category)}"><span class="segment-overlay"></span><span class="segment-badge">${brands.size} merek</span></div>
        <h3>${escapeHtml(category)}</h3><p>${products.length} shade dan varian tersedia</p><span class="segment-arrow">→</span>
      </button>`;
    }).join(''):'<div class="empty-state"><strong>Belum ada produk aktif.</strong><br>Tambahkan produk terlebih dahulu melalui tombol ＋ Produk.</div>';
    $$('[data-open-category]').forEach((button)=>button.addEventListener('click',()=>setCatalogLevel('brand',button.dataset.openCategory,'')));
    return;
  }
  if(state.level==='brand'){
    const products=categoryProducts(state.selectedCategory);const brands=[...new Set(products.map((p)=>p.brand))].sort();
    updateCatalogHeader('brand',brands.length);
    grid.className='product-grid segment-grid brand-view';
    grid.innerHTML=brands.map((brand)=>{
      const variants=brandProducts(state.selectedCategory,brand);const [a,b]=brandGradient(brand);
      return `<button class="segment-card brand-card" data-open-brand="${escapeHtml(brand)}">
        <div class="brand-visual" style="--brand-a:${a};--brand-b:${b}"><span class="brand-wordmark">${escapeHtml(brand)}</span></div>
        <span class="segment-badge">${variants.length} varian</span><h3>${escapeHtml(brand)}</h3><p>${escapeHtml(state.selectedCategory)} tersedia dalam beberapa shade atau jenis</p><span class="segment-arrow">→</span>
      </button>`;
    }).join('');
    $$('[data-open-brand]').forEach((button)=>button.addEventListener('click',()=>setCatalogLevel('product',state.selectedCategory,button.dataset.openBrand)));
    return;
  }
  const products=brandProducts(state.selectedCategory,state.selectedBrand);
  updateCatalogHeader('product',products.length);
  grid.className='product-grid segment-grid product-view';
  grid.innerHTML=products.length?products.map(productCard).join(''):'<div class="empty-state">Belum ada varian pada merek ini.</div>';
  bindProductCards();
}

function updateCatalogHeader(mode,count){
  const back=$('#catalogBack');const breadcrumb=$('#catalogBreadcrumb');
  if(mode==='search'){
    $('#catalogEyebrow').textContent='HASIL PENCARIAN';$('#catalogTitle').textContent=`Hasil untuk “${state.search.trim()}”`;$('#catalogDescription').textContent='Klik produk untuk langsung menambahkannya ke keranjang.';$('#catalogCount').textContent=`${count} produk`;back.hidden=false;back.textContent='× Hapus pencarian';
    breadcrumb.innerHTML='<button class="crumb" data-crumb="category">Jenis Produk</button><span class="crumb-separator">/</span><button class="crumb current">Pencarian</button>';
  } else if(mode==='category'){
    $('#catalogEyebrow').textContent='KATALOG SKINCARE & BEAUTY';$('#catalogTitle').textContent='Pilih Jenis Produk';$('#catalogDescription').textContent='Pilih kategori seperti Bedak, Lipstik, Serum, dan lainnya.';$('#catalogCount').textContent=`${count} jenis`;back.hidden=true;
    breadcrumb.innerHTML='<button class="crumb current">Jenis Produk</button>';
  } else if(mode==='brand'){
    $('#catalogEyebrow').textContent='LANGKAH 2 DARI 3';$('#catalogTitle').textContent=`Pilih Merek ${state.selectedCategory}`;$('#catalogDescription').textContent='Setelah merek dipilih, shade atau jenis produk akan ditampilkan.';$('#catalogCount').textContent=`${count} merek`;back.hidden=false;back.textContent='← Kembali';
    breadcrumb.innerHTML=`<button class="crumb" data-crumb="category">Jenis Produk</button><span class="crumb-separator">/</span><button class="crumb current">${escapeHtml(state.selectedCategory)}</button>`;
  } else {
    $('#catalogEyebrow').textContent='LANGKAH 3 DARI 3';$('#catalogTitle').textContent=`${state.selectedBrand} · ${state.selectedCategory}`;$('#catalogDescription').textContent='Pilih shade, jenis, atau ukuran yang dibeli pelanggan.';$('#catalogCount').textContent=`${count} varian`;back.hidden=false;back.textContent='← Kembali';
    breadcrumb.innerHTML=`<button class="crumb" data-crumb="category">Jenis Produk</button><span class="crumb-separator">/</span><button class="crumb" data-crumb="brand">${escapeHtml(state.selectedCategory)}</button><span class="crumb-separator">/</span><button class="crumb current">${escapeHtml(state.selectedBrand)}</button>`;
  }
  $$('[data-crumb="category"]').forEach((el)=>el.addEventListener('click',()=>setCatalogLevel('category')));
  $$('[data-crumb="brand"]').forEach((el)=>el.addEventListener('click',()=>setCatalogLevel('brand',state.selectedCategory)));
}

function productCard(product){
  return `<button class="product-card ${product.stock<=0?'out':''}" data-add-product="${product.id}" ${product.stock<=0?'disabled':''}>
    <img class="product-image" src="${escapeHtml(safeImage(product.image))}" alt="${escapeHtml(product.brand+' '+product.name)}">
    ${product.stock>0?'<span class="add-badge">＋</span>':''}
    <div class="product-meta"><p class="product-kicker">${escapeHtml(product.brand)} · ${escapeHtml(product.category)}</p><h3>${escapeHtml(product.name)}</h3><p class="variant-line">${escapeHtml(product.variant)}</p><p class="sku-line">${escapeHtml(product.sku)}</p><div class="product-bottom"><strong>${rupiah(product.price)}</strong><span class="stock-pill">Stok ${product.stock}</span></div></div>
  </button>`;
}
function bindProductCards(){$$('[data-add-product]').forEach((button)=>button.addEventListener('click',()=>addToCart(Number(button.dataset.addProduct))));}

function addToCart(productId){
  const product=state.products.find((item)=>item.id===productId);if(!product||product.stock<=0)return;
  const current=state.cart.get(productId);if(current&&current.quantity>=product.stock)return toast('Jumlah sudah mencapai stok tersedia.',true);
  state.cart.set(productId,{product,quantity:current?current.quantity+1:1});renderCart();
}
function updateQuantity(productId,delta){
  const item=state.cart.get(productId);if(!item)return;const next=item.quantity+delta;
  if(next<=0)state.cart.delete(productId);else if(next<=item.product.stock)item.quantity=next;else return toast('Stok produk tidak mencukupi.',true);renderCart();
}
function removeFromCart(productId){state.cart.delete(productId);renderCart();}
function cartSubtotal(){return [...state.cart.values()].reduce((sum,item)=>sum+item.product.price*item.quantity,0);}
function cartDiscount(){return Math.min(cartSubtotal(),Math.max(0,Number($('#discountInput').value||0)));}
function cartTotal(){return Math.max(0,cartSubtotal()-cartDiscount());}
function renderCart(){
  const items=[...state.cart.values()];
  $('#cartItems').innerHTML=items.length?items.map(({product,quantity})=>`<div class="cart-row"><img src="${escapeHtml(safeImage(product.image))}" alt=""><div class="cart-info"><strong>${escapeHtml(product.brand)} ${escapeHtml(product.name)}</strong><span>${escapeHtml(product.variant)} · ${rupiah(product.price)}</span></div><div class="qty-control"><button data-minus="${product.id}">−</button><b>${quantity}</b><button data-plus="${product.id}">+</button><button class="remove-item" data-remove="${product.id}" title="Hapus">×</button></div></div>`).join(''):'<div class="cart-empty"><div class="empty-icon">🛒</div><strong>Belum ada produk</strong><span>Pilih jenis, merek, lalu shade produk.</span></div>';
  $$('[data-minus]').forEach((b)=>b.addEventListener('click',()=>updateQuantity(Number(b.dataset.minus),-1)));
  $$('[data-plus]').forEach((b)=>b.addEventListener('click',()=>updateQuantity(Number(b.dataset.plus),1)));
  $$('[data-remove]').forEach((b)=>b.addEventListener('click',()=>removeFromCart(Number(b.dataset.remove))));
  $('#subtotalText').textContent=rupiah(cartSubtotal());$('#totalText').textContent=rupiah(cartTotal());$('#checkoutButton').disabled=items.length===0;
}

function resetProductForm(){
  $('#productForm').reset();$('#productId').value='';$('#existingImage').value='';$('#productCategory').value=state.selectedCategory||'Bedak';$('#productBrand').value=state.selectedBrand||'Wardah';$('#productModalTitle').textContent='Tambah Produk';
}
function editProduct(id){
  const p=state.products.find((item)=>item.id===id);if(!p)return;
  $('#productId').value=p.id;$('#existingImage').value=p.image||'';$('#productName').value=p.name;$('#productSku').value=p.sku;$('#productCategory').value=p.category;$('#productBrand').value=p.brand;$('#productVariant').value=p.variant;$('#productPrice').value=p.price;$('#productStock').value=p.stock;$('#productModalTitle').textContent='Edit Produk';openModal('productModal');
}
function renderProductTable(){
  $('#productTableBody').innerHTML=state.products.length?state.products.map((p)=>`<tr><td><div class="table-product"><img src="${escapeHtml(safeImage(p.image))}" alt=""><div><strong>${escapeHtml(p.name)}</strong><small>${escapeHtml(p.variant)}</small></div></div></td><td>${escapeHtml(p.sku)}</td><td>${escapeHtml(p.category)}</td><td><strong>${escapeHtml(p.brand)}</strong><br><small>${escapeHtml(p.variant)}</small></td><td><strong>${rupiah(p.price)}</strong></td><td class="${p.stock<5?'stock-low':''}">${p.stock}</td><td><div class="action-buttons"><button class="table-button" data-edit="${p.id}">Edit</button><button class="table-button delete" data-delete="${p.id}">Hapus</button></div></td></tr>`).join(''):'<tr><td colspan="7">Belum ada produk.</td></tr>';
  $$('[data-edit]').forEach((b)=>b.addEventListener('click',()=>editProduct(Number(b.dataset.edit))));$$('[data-delete]').forEach((b)=>b.addEventListener('click',()=>deleteProduct(Number(b.dataset.delete))));
}
async function deleteProduct(id){
  const product=state.products.find((item)=>item.id===id);if(!product||!confirm(`Hapus ${product.brand} ${product.name} — ${product.variant} dari katalog?`))return;
  try{const payload=await api('delete_product',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});toast(payload.message);await loadProducts();}catch(error){toast(error.message,true);}
}

function openCheckout(){if(state.cart.size===0)return;const total=cartTotal();$('#checkoutTotal').textContent=rupiah(total);$('#paidAmount').value=total;$('#changeText').textContent=rupiah(0);openModal('checkoutModal');}
function updateChange(){const total=cartTotal();const method=$('#paymentMethod').value;if(method!=='Tunai')$('#paidAmount').value=total;$('#paidAmount').readOnly=method!=='Tunai';$('#changeText').textContent=rupiah(Math.max(0,Number($('#paidAmount').value||0)-total));}
async function saveTransaction(){
  const button=$('#confirmTransaction');const total=cartTotal();if($('#paymentMethod').value==='Tunai'&&Number($('#paidAmount').value||0)<total)return toast('Nominal pembayaran masih kurang.',true);
  button.disabled=true;button.textContent='Menyimpan...';
  const body={items:[...state.cart.values()].map(({product,quantity})=>({product_id:product.id,quantity})),discount:cartDiscount(),customer_name:$('#customerName').value,customer_phone:$('#customerPhone').value,payment_method:$('#paymentMethod').value,paid:Number($('#paidAmount').value||0),notes:$('#transactionNotes').value};
  try{const payload=await api('save_transaction',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});state.lastTransaction=payload;closeModal('checkoutModal');$('#successCode').textContent=payload.transaction_code;$('#successMessage').textContent=`Total ${rupiah(payload.total)} tersimpan. Kembalian ${rupiah(payload.change)}.`;$('#downloadReceipt').href=payload.download_url;openModal('successModal');await loadProducts();loadDashboard();}catch(error){toast(error.message,true);}finally{button.disabled=false;button.textContent='Simpan Transaksi';}
}
function normalizePhone(phone){let digits=String(phone||'').replace(/\D/g,'');if(digits.startsWith('0'))digits=`62${digits.slice(1)}`;else if(digits.startsWith('8'))digits=`62${digits}`;return digits;}
function sendWhatsapp(){if(!state.lastTransaction)return;let phone=normalizePhone(state.lastTransaction.phone);if(!phone)phone=normalizePhone(prompt('Masukkan nomor WhatsApp pelanggan (contoh 081234567890):')||'');if(!phone)return;window.open(`https://wa.me/${phone}?text=${encodeURIComponent(state.lastTransaction.whatsapp_text)}`,'_blank','noopener');}

async function loadDashboard(){
  try{const payload=await api('dashboard');const s=payload.summary||{};const recent=payload.recent||[];$('#revenueToday').textContent=rupiah(s.revenue_today);$('#transactionsToday').textContent=Number(s.transactions_today||0);$('#averageOrder').textContent=rupiah(s.average_order);$('#activeProducts').textContent=Number(s.active_products||0);renderChart(payload.chart||[]);$('#recentTransactions').innerHTML=recent.length?recent.map((trx)=>`<div class="recent-item"><div><strong>${escapeHtml(trx.transaction_code)}</strong><small>${escapeHtml(trx.customer_name||'Pelanggan umum')} · ${formatDateTime(trx.created_at)}</small></div><span>${rupiah(trx.total)}</span></div>`).join(''):'<div class="empty-state">Belum ada transaksi.</div>';}catch(error){toast(error.message,true);}
}
function renderChart(rows){const byDate=Object.fromEntries(rows.map((r)=>[r.sale_date,Number(r.revenue)]));const dates=[];for(let i=6;i>=0;i--){const d=new Date();d.setDate(d.getDate()-i);const key=`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;dates.push({key,label:d.toLocaleDateString('id-ID',{weekday:'short'}),value:byDate[key]||0});}const max=Math.max(...dates.map((d)=>d.value),1);$('#revenueChart').innerHTML=dates.map((d)=>`<div class="bar-group"><div class="bar" style="height:${Math.max(3,d.value/max*92)}%" data-value="${rupiah(d.value)}"></div><span class="bar-label">${d.label}</span></div>`).join('');}
async function loadTransactions(){try{const payload=await api('transactions');const transactions=payload.transactions||[];$('#transactionTableBody').innerHTML=transactions.length?transactions.map((trx)=>`<tr><td><strong>${escapeHtml(trx.transaction_code)}</strong></td><td>${escapeHtml(trx.customer_name||'Pelanggan umum')}<br><small>${escapeHtml(trx.customer_phone||'-')}</small></td><td>${escapeHtml(trx.payment_method)}</td><td><strong>${rupiah(trx.total)}</strong></td><td>${formatDateTime(trx.created_at)}</td><td><div class="action-buttons"><a class="table-button" target="_blank" href="receipt.php?token=${encodeURIComponent(trx.receipt_token)}">Lihat</a><a class="table-button" target="_blank" href="print_receipt.php?token=${encodeURIComponent(trx.receipt_token)}">Cetak</a></div></td></tr>`).join(''):'<tr><td colspan="6">Belum ada transaksi.</td></tr>';}catch(error){toast(error.message,true);}}
function formatDateTime(value){if(!value)return'-';const d=new Date(String(value).replace(' ','T'));if(Number.isNaN(d.getTime()))return value;return d.toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});}
function resetTransaction(){state.cart.clear();$('#discountInput').value=0;$('#customerName').value='';$('#customerPhone').value='';$('#transactionNotes').value='';closeModal('successModal');renderCart();setCatalogLevel('category');setPage('cashier');}

$$('.nav-item').forEach((item)=>item.addEventListener('click',()=>setPage(item.dataset.page)));
$$('[data-close]').forEach((button)=>button.addEventListener('click',()=>closeModal(button.dataset.close)));
$$('.modal').forEach((modal)=>modal.addEventListener('click',(event)=>{if(event.target===modal)closeModal(modal.id);}));
$('#mobileMenu').addEventListener('click',()=>$('.sidebar').classList.toggle('open'));
$('#productSearch').addEventListener('input',(event)=>{state.search=event.target.value;renderCatalog();});
$('#catalogBack').addEventListener('click',()=>{if(state.search){state.search='';$('#productSearch').value='';renderCatalog();return;}if(state.level==='product')setCatalogLevel('brand',state.selectedCategory);else if(state.level==='brand')setCatalogLevel('category');});
$('#discountInput').addEventListener('input',renderCart);
$('#clearCart').addEventListener('click',()=>{state.cart.clear();renderCart();});
$('#checkoutButton').addEventListener('click',openCheckout);
$('#paymentMethod').addEventListener('change',updateChange);
$('#paidAmount').addEventListener('input',updateChange);
$('#confirmTransaction').addEventListener('click',saveTransaction);
$('#sendWhatsapp').addEventListener('click',sendWhatsapp);
$('#printReceipt').addEventListener('click',()=>{if(state.lastTransaction)window.open(state.lastTransaction.print_url,'_blank','noopener');});
$('#newTransaction').addEventListener('click',resetTransaction);
$('#refreshButton').addEventListener('click',()=>{loadProducts();if(state.currentPage==='dashboard')loadDashboard();if(state.currentPage==='transactions')loadTransactions();});
['#addProductButton','#quickAddProduct'].forEach((selector)=>$(selector).addEventListener('click',()=>{resetProductForm();openModal('productModal');}));
$('#productForm').addEventListener('submit',async(event)=>{event.preventDefault();const submit=event.submitter;submit.disabled=true;try{const payload=await api('save_product',{method:'POST',body:new FormData(event.currentTarget)});closeModal('productModal');toast(payload.message);await loadProducts();}catch(error){toast(error.message,true);}finally{submit.disabled=false;}});
document.addEventListener('keydown',(event)=>{if(event.key==='Escape')$$('.modal.open').forEach((modal)=>closeModal(modal.id));if(event.key==='F2'){event.preventDefault();$('#productSearch').focus();}});

$('#todayDate').textContent=new Date().toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});
loadProducts();
