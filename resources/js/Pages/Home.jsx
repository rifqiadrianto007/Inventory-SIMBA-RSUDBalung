import React from 'react'

export default function Home({ title }) {
  return (
    <div style={{ textAlign: 'center', marginTop: '100px' }}>
      <h1>{title}</h1>
      <p>Selamat! Inertia dan React sudah berhasil dihubungkan ke Laravel 11 🎉</p>
    </div>
  )
}
