import React, { useEffect, useRef, useState } from 'react'
import { useDispatch } from 'react-redux'
import 'react-h5-audio-player/lib/styles.css'
import { FormattedMessage } from 'react-intl'
import {
  StyledAudioPlayer,
  StyledForm,
  StyledFormWrapper,
  StyledLabel,
  StyledNotes,
  StyledPlayerWrapper,
  StyledRadio,
  StyledText,
  StyledTextWrapper,
  StyledVideoWrapper,
} from './styled'
import { setOpenSections } from '../../redux/accordionSlice'

export default function Player({ song, nextSongTitle, prevSongTitle }) {
  const dispatch = useDispatch()
  const [bookmark, setBookmark] = useState('text')
  const player = useRef(null)

  useEffect(() => {
    player.current.audio.current.play()
  }, [])

  const handleSubmit = (event) => {
    event.preventDefault()
  }

  const handleMediaChange = (event) => {
    setBookmark(event.target.value)
  }

  const handleClickNext = () => {
    if (nextSongTitle) {
      dispatch(setOpenSections({ [nextSongTitle]: true }))
    }
  }

  const handleClickPrevious = () => {
    if (prevSongTitle) {
      dispatch(setOpenSections({ [prevSongTitle]: true }))
    }
  }

  const getSongUrl = () => {
    const url = song?.audio?.url
    if (url) {
      return `${process.env.REACT_APP_CMS}${url}`
    }
  }

  const getNotesUrl = () => {
    const url = song?.notes[0]?.url
    if (url) {
      return `${process.env.REACT_APP_CMS}${url}`
    }
  }

  return (
    <StyledPlayerWrapper>
      <StyledAudioPlayer
        showSkipControls
        src={getSongUrl()}
        ref={player}
        onClickNext={handleClickNext}
        onClickPrevious={handleClickPrevious}
        onEnded={handleClickNext}
      />
      <StyledFormWrapper>
        <StyledForm onSubmit={handleSubmit}>
          <StyledLabel htmlFor="text">
            <StyledRadio
              type="radio"
              name="content-type"
              value="text"
              id="text"
              checked={bookmark === 'text'}
              onChange={handleMediaChange}
            />
            <FormattedMessage id="text" />
          </StyledLabel>
          <StyledLabel htmlFor="notes">
            <StyledRadio
              type="radio"
              name="content-type"
              value="notes"
              id="notes"
              checked={bookmark === 'notes'}
              onChange={handleMediaChange}
            />
            <FormattedMessage id="notes" />
          </StyledLabel>
          <StyledLabel htmlFor="video">
            <StyledRadio
              type="radio"
              name="content-type"
              value="video"
              id="video"
              checked={bookmark === 'video'}
              onChange={handleMediaChange}
            />
            <FormattedMessage id="video" />
          </StyledLabel>
        </StyledForm>
      </StyledFormWrapper>

      {bookmark === 'text' && (
        <StyledTextWrapper>
          <StyledText>{song.text_ukr}</StyledText>
          <StyledText>{song.text_eng}</StyledText>
        </StyledTextWrapper>
      )}
      {bookmark === 'notes' && (
        <StyledTextWrapper>
          <StyledNotes src={getNotesUrl()} />
        </StyledTextWrapper>
      )}
      {bookmark === 'video' && (
        <StyledVideoWrapper>
          <iframe
            width="560"
            height="315"
            src={song.youtude_video}
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
            title="Youtube"
          />
        </StyledVideoWrapper>
      )}
    </StyledPlayerWrapper>
  )
}
