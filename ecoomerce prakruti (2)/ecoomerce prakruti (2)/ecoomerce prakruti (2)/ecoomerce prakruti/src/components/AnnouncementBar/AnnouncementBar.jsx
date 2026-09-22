import React from 'react'
import './AnnouncementBar.css'

const AnnouncementBar = () => {
  return (
    <div className="announcement-bar">
      <div className="announcement-content">
        <span className="leaf-icon">🌿</span>
        <span className="announcement-text">
          Free shipping on orders above ₹999&nbsp;&nbsp;|&nbsp;&nbsp;100% Natural&nbsp;&nbsp;|&nbsp;&nbsp;Trusted by Thousands
        </span>
        <span className="leaf-icon">🌿</span>
      </div>
    </div>
  )
}

export default AnnouncementBar
