import React from 'react'
import './PrakrutiPromise.css'
import organicOriginsImg from '../../assets/images/organic origins.png'
import pureIngredientsImg from '../../assets/images/pure indgrdients.png'
import pantryImg from '../../assets/images/pantry.png'
import goodnessHomeImg from '../../assets/images/godness home.png'

const promiseCards = [
  {
    number: '01',
    title: 'Organic origins',
    description: 'Our story starts with organic grains, pulses and spices.',
    image: organicOriginsImg,
    alt: 'Organic grains growing in a field',
  },
  {
    number: '02',
    title: 'Pure ingredients',
    description: 'The pantry essentials at the heart of everyday cooking.',
    image: pureIngredientsImg,
    alt: 'Pure pulses, rice and spices in serving bowls',
  },
  {
    number: '03',
    title: 'Ready for your pantry',
    description: 'Discover your favourites in the Prakruti collection.',
    image: pantryImg,
    alt: 'Prakruti organic product packaging',
  },
  {
    number: '04',
    title: 'Goodness at home',
    description: 'Bring organic essentials into your daily meals.',
    image: goodnessHomeImg,
    alt: 'A wholesome meal made with organic essentials',
  },
]

const PrakrutiPromise = () => (
  <section className="prakruti-promise" aria-labelledby="prakruti-promise-title">
    <div className="container">
      <div className="promise-intro">
        <p className="promise-eyebrow">The Prakruti promise</p>
        <span className="promise-eyebrow-line" aria-hidden="true" />
        <h2 id="prakruti-promise-title" className="promise-title">
          Pure goodness. <em>Every step of the way.</em>
        </h2>
        <p className="promise-subtitle">A simple journey from organic ingredients to your everyday kitchen.</p>
      </div>

      <div className="prakruti-promise-grid">
        {promiseCards.map((card, index) => (
          <React.Fragment key={card.title}>
            <article className="prakruti-promise-card">
              <span className="promise-number">{card.number}</span>
              <div className="promise-image-wrap">
                <img src={card.image} alt={card.alt} className="promise-image" loading="lazy" />
              </div>
              <div className="promise-card-content">
                <h3>{card.title}</h3>
                <p>{card.description}</p>
              </div>
            </article>
            {index < promiseCards.length - 1 && <span className="prakruti-promise-arrow" aria-hidden="true" />}
          </React.Fragment>
        ))}
      </div>

      <a className="promise-cta" href="#categories">
        Shop organic essentials <span aria-hidden="true">-&gt;</span>
      </a>
    </div>
  </section>
)

export default PrakrutiPromise
