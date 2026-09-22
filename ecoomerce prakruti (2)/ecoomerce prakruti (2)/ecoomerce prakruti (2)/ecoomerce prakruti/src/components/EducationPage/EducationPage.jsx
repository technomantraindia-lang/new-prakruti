import React, { useMemo, useState } from 'react'
import './EducationPage.css'
import { apiClient } from '../../api/client'

// Import assets
import gheeImg from '../../assets/images/ghee.png'
import oilImg from '../../assets/images/oil.png'
import packagingImg from '../../assets/images/packgin.png'
import spicesImg from '../../assets/images/indian_spices.png'
import bannerBg from '../../assets/images/eA.png'

const COMPARISONS = [
  {
    topic: 'Soil & Cultivation',
    organic: {
      title: 'Organic Farming (Prakruti)',
      desc: 'Restores soil biodiversity through crop rotation, animal manures, and bio-compost. Retains high water-holding capacity and mineral absorption.',
      status: 'purity-good'
    },
    conventional: {
      title: 'Conventional Farming',
      desc: 'Relies heavily on synthetic chemical fertilizers. Over time, strips the soil of native microbes, leaving it depleted and mineral-deficient.',
      status: 'purity-bad'
    }
  },
  {
    topic: 'Pest Management',
    organic: {
      title: 'Prakruti Bio-Remedies',
      desc: 'Utilizes neem extract, insect traps, and natural predators. Zero chemical residues, preserving native ecosystems and bee populations.',
      status: 'purity-good'
    },
    conventional: {
      title: 'Synthetic Pesticides',
      desc: 'Uses chemical sprays (like glyphosate). Traces of these toxic agents remain on crops and leak into groundwater supplies.',
      status: 'purity-bad'
    }
  },
  {
    topic: 'Processing & Polish',
    organic: {
      title: 'Unpolished & Raw',
      desc: 'Grains and dals are left unpolished with their nutrient-rich bran and fiber layer intact. No synthetic oils or polishing powders added.',
      status: 'purity-good'
    },
    conventional: {
      title: 'Chemically Polished',
      desc: 'Dals are polished using leather, soapstone powder, or synthetic oils to look shiny. This completely strips away dietary fiber and B-complex vitamins.',
      status: 'purity-bad'
    }
  }
]

const HEIGHT_UNITS = {
  FEET: 'feet',
  CM: 'cm'
}

const BMI_DISCLAIMER = 'BMI is a general screening measure and does not directly measure body fat or diagnose a health condition.'
const UNDER_18_MESSAGE = 'BMI interpretation for children and teenagers depends on age and sex and requires an appropriate pediatric BMI assessment.'

const BMI_WELLNESS_TIPS = [
  'Build balanced meals with grains, pulses, vegetables, healthy fats and adequate protein.',
  'Stay hydrated through the day and avoid replacing meals with sugary drinks.',
  'Keep daily activity consistent, such as walking, mobility work or any movement you enjoy.',
  'Use mindful portions and regular meal timing instead of extreme dieting.'
]

const BMI_PRODUCT_RECOMMENDATIONS = {
  Underweight: ['Cereals & Pulses', 'A2 Ghee', 'Healthy Seeds'],
  'Healthy Weight': ['Cold-Pressed Oils', 'Organic Spices', 'Healthy Seeds'],
  Overweight: ['Millets', 'Unpolished Dals', 'Natural Sweeteners'],
  Obesity: ['Millets', 'Unpolished Dals', 'Cold-Pressed Oils']
}

const parseFiniteNumber = (value) => {
  if (typeof value !== 'string' || value.trim() === '') {
    return null
  }

  const parsed = Number(value)
  return Number.isFinite(parsed) ? parsed : null
}

const feetAndInchesToMeters = (feet, inches) => ((feet * 12) + inches) * 0.0254
const centimetresToMeters = (centimetres) => centimetres / 100
const calculateBmiValue = (weightKg, heightInMeters) => weightKg / (heightInMeters * heightInMeters)
const roundBmiForDisplay = (bmi) => Math.round(bmi * 10) / 10
const formatHeightCm = (cm) => String(Math.round(cm * 10) / 10).replace(/\.0$/, '')
const convertFeetInchesToCm = (feetValue, inchesValue) => ((feetValue * 12) + inchesValue) * 2.54
const convertCmToFeetInches = (cmValue) => {
  const totalInches = cmValue / 2.54
  let feetValue = Math.floor(totalInches / 12)
  let inchesValue = Math.round(totalInches % 12)

  if (inchesValue === 12) {
    feetValue += 1
    inchesValue = 0
  }

  return { feet: feetValue, inches: inchesValue }
}

const classifyAdultBmi = (bmi) => {
  if (bmi < 18.5) {
    return {
      category: 'Underweight',
      className: 'bmi-underweight',
      description: 'Your BMI is below the standard healthy-weight range for adults.'
    }
  }

  if (bmi < 25) {
    return {
      category: 'Healthy Weight',
      className: 'bmi-healthy',
      description: 'Your BMI falls within the standard healthy-weight range for adults.'
    }
  }

  if (bmi < 30) {
    return {
      category: 'Overweight',
      className: 'bmi-overweight',
      description: 'Your BMI is above the standard adult healthy weight range.'
    }
  }

  return {
      category: 'Obesity',
      className: 'bmi-obesity',
      description: 'Your BMI is in the standard adult obesity range.'
  }
}

const EducationPage = ({ onShopClick }) => {
  // Quiz states
  const [currentQuestionIdx, setCurrentQuestionIdx] = useState(0)
  const [selectedOptionIdx, setSelectedOptionIdx] = useState(null)
  const [quizAnswers, setQuizAnswers] = useState([])
  const [quizFinished, setQuizFinished] = useState(false)

  const handleOptionSelect = (optionIdx) => {
    setSelectedOptionIdx(optionIdx)
  }

  const handleNextQuestion = () => {
    if (selectedOptionIdx === null) return

    const selectedScore = QUIZ_QUESTIONS[currentQuestionIdx].options[selectedOptionIdx].score
    const updatedAnswers = [...quizAnswers, selectedScore]
    setQuizAnswers(updatedAnswers)

    if (currentQuestionIdx < QUIZ_QUESTIONS.length - 1) {
      setCurrentQuestionIdx(currentQuestionIdx + 1)
      setSelectedOptionIdx(null)
    } else {
      setQuizFinished(true)
    }
  }

  const handleResetQuiz = () => {
    setCurrentQuestionIdx(0)
    setSelectedOptionIdx(null)
    setQuizAnswers([])
    setQuizFinished(false)
  }

  const calculateTotalScore = () => {
    return quizAnswers.reduce((sum, score) => sum + score, 0)
  }

  const getQuizResult = () => {
    const total = calculateTotalScore()
    if (total <= 6) {
      return {
        level: 'Chemical Heavy diet ⚠️',
        description: 'Your diet heavily consists of refined and chemically-altered ingredients. Switching to unrefined staples and organic cold-pressed products can significantly reduce toxicity levels.',
        class: 'result-alert'
      }
    } else if (total <= 10) {
      return {
        level: 'Moderately Conscious 🌾',
        description: 'You are making decent food choices, but there is still room for improvement. Replacing refined oils with cold-pressed oils and polished pulses with raw organic ones will boost your nutritional value.',
        class: 'result-moderate'
      }
    } else {
      return {
        level: 'Organic Champion! 🌱👑',
        description: 'Congratulations! You prioritize wholesome, traditional, and unadulterated foods. Your body enjoys natural antioxidants, clean fiber, and healthy fats.',
        class: 'result-excellent'
      }
    }
  }

  const quizResult = quizFinished ? getQuizResult() : null
  const [consultationForm, setConsultationForm] = useState({
    name: '',
    email: '',
    phone: '',
    contact_method: 'email',
    concern: '',
    age_range: '',
    dietary_preference: '',
    preferred_time: '',
    consent: false
  })
  const [consultationStatus, setConsultationStatus] = useState({ type: '', message: '' })
  const [submittingConsultation, setSubmittingConsultation] = useState(false)
  const [heightCm, setHeightCm] = useState('')
  const [weightKg, setWeightKg] = useState('')

  const bmi = useMemo(() => {
    const height = Number(heightCm)
    const weight = Number(weightKg)

    if (!height || !weight || height <= 0 || weight <= 0) {
      return null
    }

    return weight / (height ** 2)
  }, [heightCm, weightKg])

  const bmiResult = useMemo(() => {
    if (!bmi) {
      return null
    }

    if (bmi < 18.5) {
      return {
        level: 'Underweight',
        class: 'result-alert',
        description: 'Your BMI is below the healthy range. Focus on balanced meals with clean grains, dals, ghee, nuts and protein-rich staples.'
      }
    }

    if (bmi < 25) {
      return {
        level: 'Healthy Range',
        class: 'result-excellent',
        description: 'Great! Your BMI is in the healthy range. Keep supporting it with pure, natural staples and mindful daily meals.'
      }
    }

    if (bmi < 30) {
      return {
        level: 'Overweight',
        class: 'result-moderate',
        description: 'Your BMI is slightly above the healthy range. Choose clean everyday staples, portion wisely and stay regularly active.'
      }
    }

    return {
      level: 'Obese Range',
      class: 'result-alert',
      description: 'Your BMI is above the recommended range. Prefer wholesome pantry choices and consider guidance from a healthcare professional.'
    }
  }, [bmi])

  const [heightUnit, setHeightUnit] = useState(HEIGHT_UNITS.FEET)
  const [age, setAge] = useState('')
  const [feet, setFeet] = useState('')
  const [inches, setInches] = useState('')
  const [errors, setErrors] = useState({})
  const [calculatedBmiResult, setCalculatedBmiResult] = useState(null)

  const clearCalculatedResult = () => {
    setCalculatedBmiResult(null)
  }

  const handleHeightUnitChange = (unit) => {
    if (unit === heightUnit) return

    if (heightUnit === HEIGHT_UNITS.FEET && unit === HEIGHT_UNITS.CM) {
      const parsedFeet = parseFiniteNumber(feet)
      const parsedInches = inches.trim() === '' ? 0 : parseFiniteNumber(inches)

      if (
        parsedFeet !== null &&
        Number.isInteger(parsedFeet) &&
        parsedFeet > 0 &&
        parsedInches !== null &&
        Number.isInteger(parsedInches) &&
        parsedInches >= 0 &&
        parsedInches <= 11
      ) {
        setHeightCm(formatHeightCm(convertFeetInchesToCm(parsedFeet, parsedInches)))
      }
    }

    if (heightUnit === HEIGHT_UNITS.CM && unit === HEIGHT_UNITS.FEET) {
      const parsedHeightCm = parseFiniteNumber(heightCm)

      if (parsedHeightCm !== null && parsedHeightCm > 0) {
        const converted = convertCmToFeetInches(parsedHeightCm)
        setFeet(String(converted.feet))
        setInches(String(converted.inches))
      }
    }

    setHeightUnit(unit)
    setErrors({})
    setCalculatedBmiResult(null)
  }

  const validateBmiInputs = () => {
    const nextErrors = {}
    const parsedAge = parseFiniteNumber(age)
    const parsedWeight = parseFiniteNumber(weightKg)
    const parsedFeet = parseFiniteNumber(feet)
    const parsedInches = parseFiniteNumber(inches)
    const parsedHeightCm = parseFiniteNumber(heightCm)

    if (parsedAge === null || parsedAge <= 0) {
      nextErrors.age = 'Enter a valid positive age.'
    } else if (parsedAge < 18) {
      nextErrors.age = UNDER_18_MESSAGE
    }

    if (parsedWeight === null || parsedWeight <= 0) {
      nextErrors.weightKg = 'Weight must be greater than 0 kg.'
    }

    if (heightUnit === HEIGHT_UNITS.FEET) {
      if (parsedFeet === null || !Number.isInteger(parsedFeet) || parsedFeet <= 0) {
        nextErrors.feet = 'Feet must be a positive whole number.'
      }

      if (parsedInches === null || !Number.isInteger(parsedInches) || parsedInches < 0 || parsedInches > 11) {
        nextErrors.inches = 'Inches must be a whole number between 0 and 11.'
      }
    } else if (parsedHeightCm === null || parsedHeightCm <= 0) {
      nextErrors.heightCm = 'Height in centimetres must be greater than 0.'
    }

    return {
      errors: nextErrors,
      values: {
        weightKg: parsedWeight,
        feet: parsedFeet,
        inches: parsedInches,
        heightCm: parsedHeightCm
      }
    }
  }

  const handleCalculateBmi = () => {
    const validation = validateBmiInputs()
    setErrors(validation.errors)
    setCalculatedBmiResult(null)

    if (Object.keys(validation.errors).length > 0) {
      return
    }

    const heightInMeters = heightUnit === HEIGHT_UNITS.FEET
      ? feetAndInchesToMeters(validation.values.feet, validation.values.inches)
      : centimetresToMeters(validation.values.heightCm)

    const preciseBmi = calculateBmiValue(validation.values.weightKg, heightInMeters)
    const displayBmi = roundBmiForDisplay(preciseBmi)

    setCalculatedBmiResult({
      bmi: displayBmi,
      preciseBmi,
      ...classifyAdultBmi(preciseBmi)
    })
  }

  const handleResetBmi = () => {
    setHeightUnit(HEIGHT_UNITS.FEET)
    setAge('')
    setFeet('')
    setInches('')
    setHeightCm('')
    setWeightKg('')
    setErrors({})
    setCalculatedBmiResult(null)
  }

  const updateConsultationField = (field, value) => {
    setConsultationForm((current) => ({ ...current, [field]: value }))
    setConsultationStatus({ type: '', message: '' })
  }

  const handleConsultationSubmit = async (event) => {
    event.preventDefault()
    setConsultationStatus({ type: '', message: '' })

    if (!consultationForm.name.trim() || !consultationForm.concern.trim()) {
      setConsultationStatus({ type: 'error', message: 'Please add your name and wellness goal/concern.' })
      return
    }

    if (!consultationForm.email.trim() && !consultationForm.phone.trim()) {
      setConsultationStatus({ type: 'error', message: 'Please add either email or mobile number.' })
      return
    }

    if (!consultationForm.consent) {
      setConsultationStatus({ type: 'error', message: 'Please accept consent before submitting the request.' })
      return
    }

    setSubmittingConsultation(true)
    const res = await apiClient('/consultation-requests', {
      method: 'POST',
      body: JSON.stringify(consultationForm)
    })
    setSubmittingConsultation(false)

    if (res.success) {
      setConsultationStatus({
        type: 'success',
        message: `Request saved successfully. Reference: ${res.data?.reference_no || 'CONSULTATION'}. Our expert will contact you soon.`
      })
      setConsultationForm({
        name: '',
        email: '',
        phone: '',
        contact_method: 'email',
        concern: '',
        age_range: '',
        dietary_preference: '',
        preferred_time: '',
        consent: false
      })
    } else {
      setConsultationStatus({ type: 'error', message: res.message || 'Could not submit request. Please try again.' })
    }
  }

  return (
    <div className="education-page">
      {/* 1. Hero Section */}
      <section className="edu-hero" style={{ backgroundImage: `url(${bannerBg})` }}>
      </section>

      {/* 2. Educational Section: Cold Pressed Oils vs Refined */}
      <section className="edu-section">
        <div className="container">
          <div className="edu-row">
            <div className="edu-col-text">
              <h2 className="section-title">The Science of Wooden Ghani</h2>
              <p>Commercial refined oils undergo high-heat extraction (exceeding 230°C) along with solvent extraction (using petroleum-derived hexane) and chemical bleaching. This strips away all natural vitamin E, antioxidants, and essential fatty acids, transforming healthy fats into harmful trans-fats.</p>
              <p>At <strong>Prakruti</strong>, our seeds are pressed in traditional wood Ghani crushers at slow speeds. Since the temperature never exceeds 40°C, the natural molecular structure of the oils remains intact, delivering the full flavor, aroma, and heart-healthy nutrients that nature intended.</p>
              
              <div className="edu-benefits-list">
                <div className="edu-benefit-item">
                  <span className="benefit-icon">💡</span>
                  <div>
                    <h4>No Chemical Hexane</h4>
                    <p>Pressed mechanically without toxic chemical solvents or chemical bleaching agents.</p>
                  </div>
                </div>
                <div className="edu-benefit-item">
                  <span className="benefit-icon">🌡️</span>
                  <div>
                    <h4>Low-Temperature Extraction</h4>
                    <p>Prevents nutrient denaturation, maintaining natural tocopherols and Omega-3 fats.</p>
                  </div>
                </div>
              </div>
            </div>

            <div className="edu-col-img">
              <div className="edu-img-frame">
                <img src={oilImg} alt="Cold pressed oils seeds and wooden ghani extraction" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 3. Comparative Grid: Organic vs Conventional */}
      <section className="comparison-section">
        <div className="container">
          <div className="text-center section-header-center">
            <span className="section-subtitle">TRANSPARENCY</span>
            <h2 className="section-title">Conventional vs. Organic Prakruti</h2>
            <p>A closer look at how standard farming stacks up against certified organic methods.</p>
          </div>

          <div className="comparison-grid">
            {COMPARISONS.map((comp, idx) => (
              <div key={idx} className="comparison-card">
                <h3 className="comparison-topic">{comp.topic}</h3>
                <div className="comparison-columns">
                  <div className={`comparison-col ${comp.organic.status}`}>
                    <h4><span className="purity-icon purity-check">✓</span> {comp.organic.title}</h4>
                    <p>{comp.organic.desc}</p>
                  </div>
                  <div className={`comparison-col ${comp.conventional.status}`}>
                    <h4><span className="purity-icon purity-cross">✗</span> {comp.conventional.title}</h4>
                    <p>{comp.conventional.desc}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. Traditional Bilora Ghee & Unpolished grains */}
      <section className="edu-section light-bg">
        <div className="container">
          <div className="edu-row reverse">
            <div className="edu-col-text">
              <h2 className="section-title">Traditional Desi Cow Bilona Ghee</h2>
              <p>Prakruti ghee is made for families who want pure, daily nourishment from real desi cow milk, not shortcut cream-based ghee. Fresh A2 milk from cared-for Indian cows is first set into curd, then slowly churned using the traditional Bilona method to separate cultured butter.</p>
              <p>The butter is gently slow-cooked in small batches until it becomes golden, aromatic, and naturally grainy. This careful process preserves the rich taste, easy digestibility, and wholesome fats that make desi cow ghee a trusted part of Indian kitchens for roti, dal, khichdi, sweets, and everyday wellness.</p>
              <button className="shop-cta-btn" onClick={onShopClick}>
                Explore A2 Ghee & Staples →
              </button>
            </div>
            
            <div className="edu-col-img">
              <div className="edu-img-frame">
                <img src={gheeImg} alt="Vedic Bilona Churned Ghee in glass jar" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 5. Consultation Request + BMI Guidance */}
      <section className="quiz-section wellness-tools-section">
        <div className="container">
          <div className="consultation-request-box" id="consultation-request">
            <div className="consultation-copy">
              <span className="quiz-badge">CONSULT AN EXPERT</span>
              <h2 className="quiz-main-title">Request a Nutritionist or Doctor Consultation</h2>
              <p className="quiz-subtitle">
                Tell us your basic details, dietary concern, and preferred contact method. Our team will review your request and help connect you with the right expert guidance.
              </p>
              <div className="consultation-flow">
                <span>Fill details</span>
                <span>Consent + submit</span>
                <span>Expert reviews</span>
                <span>Email / WhatsApp follow-up</span>
              </div>
            </div>

            <form className="consultation-form" onSubmit={handleConsultationSubmit}>
              <div className="consultation-grid">
                <label>
                  <span>Name *</span>
                  <input type="text" value={consultationForm.name} onChange={(event) => updateConsultationField('name', event.target.value)} placeholder="Your full name" />
                </label>
                <label>
                  <span>Email</span>
                  <input type="email" value={consultationForm.email} onChange={(event) => updateConsultationField('email', event.target.value)} placeholder="you@example.com" />
                </label>
                <label>
                  <span>Mobile Number</span>
                  <input type="tel" value={consultationForm.phone} onChange={(event) => updateConsultationField('phone', event.target.value)} placeholder="Mobile number" />
                </label>
                <label>
                  <span>Preferred Contact *</span>
                  <select value={consultationForm.contact_method} onChange={(event) => updateConsultationField('contact_method', event.target.value)}>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="phone">Phone Call</option>
                  </select>
                </label>
                <label>
                  <span>Age Range</span>
                  <select value={consultationForm.age_range} onChange={(event) => updateConsultationField('age_range', event.target.value)}>
                    <option value="">Select age range</option>
                    <option value="18-25">18-25</option>
                    <option value="26-35">26-35</option>
                    <option value="36-50">36-50</option>
                    <option value="51-65">51-65</option>
                    <option value="65+">65+</option>
                  </select>
                </label>
                <label>
                  <span>Dietary Preference</span>
                  <select value={consultationForm.dietary_preference} onChange={(event) => updateConsultationField('dietary_preference', event.target.value)}>
                    <option value="">Select preference</option>
                    <option value="vegetarian">Vegetarian</option>
                    <option value="vegan">Vegan</option>
                    <option value="jain">Jain</option>
                    <option value="mixed">Mixed diet</option>
                  </select>
                </label>
                <label>
                  <span>Preferred Time</span>
                  <input type="text" value={consultationForm.preferred_time} onChange={(event) => updateConsultationField('preferred_time', event.target.value)} placeholder="e.g. Weekdays after 5 PM" />
                </label>
                <label className="consultation-textarea">
                  <span>Concern / Goal *</span>
                  <textarea rows="4" value={consultationForm.concern} onChange={(event) => updateConsultationField('concern', event.target.value)} placeholder="Tell us your general goal, such as balanced meals, pantry planning, weight management support, or family nutrition guidance." />
                </label>
              </div>

              <label className="consultation-consent">
                <input type="checkbox" checked={consultationForm.consent} onChange={(event) => updateConsultationField('consent', event.target.checked)} />
                <span>I consent to share these submitted details with Prakruti's assigned nutrition professional for follow-up. I understand this is not emergency medical care.</span>
              </label>

              <p className="consultation-privacy-note">Privacy note: you may request correction or deletion of your submitted details as per the applicable Prakruti policy.</p>

              {consultationStatus.message && (
                <div className={`consultation-status ${consultationStatus.type}`}>{consultationStatus.message}</div>
              )}

              <button className="quiz-next-btn" type="submit" disabled={submittingConsultation}>
                {submittingConsultation ? 'Submitting...' : 'Submit Consultation Request'}
              </button>
            </form>
          </div>

          <div className="quiz-container-box bmi-calculator-box">
            <div className="quiz-active-view">
              <span className="quiz-badge">BMI CALCULATOR & WELLNESS GUIDANCE</span>
              <h2 className="quiz-main-title">Calculate Your BMI</h2>
              <p className="quiz-subtitle">Enter height and weight to calculate adult Body Mass Index. Formula: BMI = weight (kg) / height (m)².</p>

              <div className="bmi-form-grid">
                <div className={`bmi-field-group ${errors.age ? 'has-error' : ''}`}>
                  <label className="bmi-field-label" htmlFor="bmi-age">
                    Age <span className="bmi-label-unit">(Years)</span>
                  </label>
                  <div className="bmi-input-control">
                    <input
                      id="bmi-age"
                      type="number"
                      min="18"
                      inputMode="decimal"
                      value={age}
                      onChange={(event) => {
                        setAge(event.target.value)
                        clearCalculatedResult()
                      }}
                      aria-describedby={errors.age ? 'bmi-age-error' : undefined}
                      placeholder="32"
                    />
                    <span className="bmi-input-affix">yrs</span>
                  </div>
                  {errors.age && <small className="bmi-error" id="bmi-age-error">{errors.age}</small>}
                </div>

                <div className={`bmi-field-group ${errors.weightKg ? 'has-error' : ''}`}>
                  <label className="bmi-field-label" htmlFor="bmi-weight">
                    Weight <span className="bmi-label-unit">(Kilograms)</span>
                  </label>
                  <div className="bmi-input-control">
                    <input
                      id="bmi-weight"
                      type="number"
                      min="0"
                      inputMode="decimal"
                      value={weightKg}
                      onChange={(event) => {
                        setWeightKg(event.target.value)
                        clearCalculatedResult()
                      }}
                      aria-describedby={errors.weightKg ? 'bmi-weight-error' : undefined}
                      placeholder="70"
                    />
                    <span className="bmi-input-affix">kg</span>
                  </div>
                  {errors.weightKg && <small className="bmi-error" id="bmi-weight-error">{errors.weightKg}</small>}
                </div>
              </div>

              <div className="bmi-toggle-wrapper">
                <div className="bmi-unit-segmented-control" role="radiogroup" aria-label="Height unit">
                  <button
                    type="button"
                    className={`bmi-segment-btn ${heightUnit === HEIGHT_UNITS.FEET ? 'active' : ''}`}
                    aria-pressed={heightUnit === HEIGHT_UNITS.FEET}
                    onClick={() => handleHeightUnitChange(HEIGHT_UNITS.FEET)}
                  >
                    Feet & Inches
                  </button>
                  <button
                    type="button"
                    className={`bmi-segment-btn ${heightUnit === HEIGHT_UNITS.CM ? 'active' : ''}`}
                    aria-pressed={heightUnit === HEIGHT_UNITS.CM}
                    onClick={() => handleHeightUnitChange(HEIGHT_UNITS.CM)}
                  >
                    Centimetres
                  </button>
                </div>
              </div>

              {heightUnit === HEIGHT_UNITS.FEET ? (
                <div className="bmi-form-grid bmi-height-grid">
                  <div className={`bmi-field-group ${errors.feet ? 'has-error' : ''}`}>
                    <label className="bmi-field-label" htmlFor="bmi-feet">
                      Feet
                    </label>
                    <div className="bmi-input-control">
                      <input
                        id="bmi-feet"
                        type="number"
                        min="1"
                        inputMode="numeric"
                        value={feet}
                        onChange={(event) => {
                          setFeet(event.target.value)
                          clearCalculatedResult()
                        }}
                        aria-describedby={errors.feet ? 'bmi-feet-error' : undefined}
                        placeholder="5"
                      />
                      <span className="bmi-input-affix">ft</span>
                    </div>
                    {errors.feet && <small className="bmi-error" id="bmi-feet-error">{errors.feet}</small>}
                  </div>

                  <div className={`bmi-field-group ${errors.inches ? 'has-error' : ''}`}>
                    <label className="bmi-field-label" htmlFor="bmi-inches">
                      Inches
                    </label>
                    <div className="bmi-input-control">
                      <input
                        id="bmi-inches"
                        type="number"
                        min="0"
                        max="11"
                        inputMode="numeric"
                        value={inches}
                        onChange={(event) => {
                          setInches(event.target.value)
                          clearCalculatedResult()
                        }}
                        aria-describedby={errors.inches ? 'bmi-inches-error' : undefined}
                        placeholder="6"
                      />
                      <span className="bmi-input-affix">in</span>
                    </div>
                    {errors.inches && <small className="bmi-error" id="bmi-inches-error">{errors.inches}</small>}
                  </div>
                </div>
              ) : (
                <div className="bmi-form-grid bmi-single-height-grid">
                  <div className={`bmi-field-group ${errors.heightCm ? 'has-error' : ''}`}>
                    <label className="bmi-field-label" htmlFor="bmi-height-cm">
                      Height <span className="bmi-label-unit">(Centimetres)</span>
                    </label>
                    <div className="bmi-input-control">
                      <input
                        id="bmi-height-cm"
                        type="number"
                        min="0"
                        inputMode="decimal"
                        value={heightCm}
                        onChange={(event) => {
                          setHeightCm(event.target.value)
                          clearCalculatedResult()
                        }}
                        aria-describedby={errors.heightCm ? 'bmi-height-cm-error' : undefined}
                        placeholder="170"
                      />
                      <span className="bmi-input-affix">cm</span>
                    </div>
                    {errors.heightCm && <small className="bmi-error" id="bmi-height-cm-error">{errors.heightCm}</small>}
                  </div>
                </div>
              )}

              <div className="bmi-action-button-group">
                <button className="bmi-btn-primary" type="button" onClick={handleCalculateBmi}>
                  Calculate BMI
                </button>
                <button className="bmi-btn-secondary" type="button" onClick={handleResetBmi}>
                  Reset
                </button>
                <button className="bmi-btn-shop" type="button" onClick={onShopClick}>
                  Shop Healthy Staples →
                </button>
              </div>

              {calculatedBmiResult && (
                <div className={`bmi-result-panel ${calculatedBmiResult.className}`}>
                  <div className="bmi-score-card">
                    <div className="bmi-score-meter">
                      <span className="score-num">{calculatedBmiResult.bmi.toFixed(1)}</span>
                      <span className="score-unit">BMI</span>
                    </div>
                    <div className="bmi-score-tag">
                      <span className="bmi-dot-indicator"></span>
                      {calculatedBmiResult.category}
                    </div>
                    <span className="bmi-normal-range">Normal: 18.5 – 24.9</span>
                  </div>

                  <div className={`bmi-summary-card ${calculatedBmiResult.className}`}>
                    <div className="bmi-summary-header">
                      <span className="bmi-summary-icon">🌱</span>
                      <h3>{calculatedBmiResult.category}</h3>
                    </div>
                    <p className="bmi-summary-desc">{calculatedBmiResult.description}</p>
                    <div className="bmi-disclaimer-note">
                      <span className="disclaimer-icon">ℹ️</span>
                      <span>{BMI_DISCLAIMER}</span>
                    </div>
                  </div>

                  <div className="bmi-guidance-card">
                    <div className="bmi-guidance-section">
                      <h4>General Wellness Tips</h4>
                      <ul className="bmi-tips-list">
                        {BMI_WELLNESS_TIPS.map((tip) => (
                          <li key={tip}>
                            <span className="tip-bullet">✓</span>
                            <span>{tip}</span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    <div className="bmi-guidance-section">
                      <h4>Recommended Staples</h4>
                      <p className="bmi-recom-sub">Pantry suggestions aligned with your nutritional balance:</p>
                      <div className="bmi-category-chips">
                        {(BMI_PRODUCT_RECOMMENDATIONS[calculatedBmiResult.category] || []).map((category) => (
                          <button type="button" key={category} className="bmi-chip-btn" onClick={onShopClick}>
                            {category}
                          </button>
                        ))}
                      </div>
                    </div>

                    <a className="consult-expert-link" href="#consultation-request">
                      Consult a Nutrition Expert →
                    </a>
                  </div>
                </div>
              )}
            </div>
          </div>

          {false && (
            <div className="quiz-container-box old-purity-quiz">
            {!quizFinished ? (
              <div className="quiz-active-view">
                <span className="quiz-badge">TEST YOUR DIET</span>
                <h2 className="quiz-main-title">Calculate Your Food Purity Score</h2>
                <p className="quiz-subtitle">Answer 4 quick questions to see how chemical-free your current pantry is.</p>

                <div className="quiz-question-box">
                  <h3 className="question-title">{QUIZ_QUESTIONS[currentQuestionIdx].question}</h3>
                  <div className="quiz-options-list">
                    {QUIZ_QUESTIONS[currentQuestionIdx].options.map((option, idx) => (
                      <button
                        key={idx}
                        className={`quiz-option-btn ${selectedOptionIdx === idx ? 'selected' : ''}`}
                        onClick={() => handleOptionSelect(idx)}
                      >
                        {option.text}
                      </button>
                    ))}
                  </div>
                </div>

                <div className="quiz-footer">
                  <span className="quiz-progress">Question {currentQuestionIdx + 1} of {QUIZ_QUESTIONS.length}</span>
                  <button
                    className="quiz-next-btn"
                    disabled={selectedOptionIdx === null}
                    onClick={handleNextQuestion}
                  >
                    {currentQuestionIdx < QUIZ_QUESTIONS.length - 1 ? 'Next Question →' : 'Submit & See Score'}
                  </button>
                </div>
              </div>
            ) : (
              <div className="quiz-result-view">
                <div className="quiz-result-icon">📊</div>
                <h2 className="quiz-main-title">Your Purity Quotient</h2>
                <div className="purity-score-display">
                  <span className="score-num">{calculateTotalScore()}</span>
                  <span className="score-denom">/ 12 Points</span>
                </div>

                <div className={`result-box-card ${quizResult.class}`}>
                  <h3>{quizResult.level}</h3>
                  <p>{quizResult.description}</p>
                </div>

                <div className="quiz-action-buttons">
                  <button className="reset-quiz-btn" onClick={handleResetQuiz}>
                    Retake Quiz ↺
                  </button>
                  <button className="quiz-shop-btn" onClick={onShopClick}>
                    Upgrade to Pure Staples →
                  </button>
                </div>
              </div>
            )}
            </div>
          )}
        </div>
      </section>
    </div>
  )
}

export default EducationPage
