import React, { useEffect, useState } from 'react'
import './AnnouncementBar.css'
import { checkoutApi } from '../../api/checkout'

const AnnouncementBar = () => {
  const [freeShippingAmount, setFreeShippingAmount] = useState(999)

  useEffect(() => {
    let mounted = true

    checkoutApi.getShippingMethods().then((res) => {
      if (!mounted || !res.success) return

      const settingThreshold = Number(res.settings?.free_shipping_min_amount || 0)
      if (settingThreshold > 0) {
        setFreeShippingAmount(settingThreshold)
        return
      }

      if (!Array.isArray(res.data)) return

      const thresholds = res.data
        .map((method) => Number(method.min_free_order || 0))
        .filter((value) => value > 0)
        .sort((a, b) => a - b)

      if (thresholds.length) {
        setFreeShippingAmount(thresholds[0])
      }
    })

    return () => {
      mounted = false
    }
  }, [])

  const formattedAmount = Number(freeShippingAmount || 999).toLocaleString('en-IN', {
    maximumFractionDigits: 0,
  })

  return (
    <div className="announcement-bar">
      <div className="announcement-content">
        <span className="leaf-icon">🌿</span>
        <span className="announcement-text">
          Free shipping on orders above ₹{formattedAmount}&nbsp;&nbsp;|&nbsp;&nbsp;Trusted by Thousands
        </span>
        <span className="leaf-icon">🌿</span>
      </div>
    </div>
  )
}

export default AnnouncementBar
