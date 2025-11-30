self.addEventListener(type:'install', listener:event =>{
	console.log('[SW]:install')
})

self.addEventListener(type:'activate', listener:event => {
console.log('[SW]:activate')
})